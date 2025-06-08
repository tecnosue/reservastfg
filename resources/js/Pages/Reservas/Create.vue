<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed, nextTick, onMounted } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";

// Props que llegan desde el backend
const props = defineProps({
    zona: {
        type: Object,
        required: true
    },
    reservas: {
        type: Array,
        default: () => []
    },
    disponibilidades: {
        type: Array,
        default: () => []
    },
});

// Variables reactivas para el estado de la reserva
const selectedDate = ref(""); //Fecha seleccionada en el calendario
const availableSlots = ref([]); // Horarios disponibles para la fecha seleccionada
const selectedSlot = ref(""); // Horario seleccionado por el usuario
const isLoading = ref(false); // Estado de carga para el envío del formulario
const calendarRef = ref(null); // Referencia al componente FullCalendar

// Formulario de reserva
const form = useForm({
    zona_id: props.zona.id,
    fecha: "",
    hora_inicio: "",
});


// Computed para agrupar horarios por día de la semana (Convierte el array de disponibilidades en un objeto agrupado por día)
// Ejemplo: { lunes: ["08:00", "09:00"], martes: ["10:00"] }
// Utiliza reduce para transformar el array en un objeto donde las claves son los días de la semana
//computed --> recalcula automáticamente cuando las disponibilidades cambian
const slotsByDay = computed(() => {
    return props.disponibilidades.reduce((acc, slot) => {
        const dia = slot.dia_semana.toLowerCase();
        if (!acc[dia]) acc[dia] = [];
        acc[dia].push(slot.hora_inicio);
        return acc;
    }, {});
});

// Computed para reservas agrupadas por fecha
// Agrupa las reservas por fecha, creando un objeto donde cada clave es una fecha y el valor es un array de horas ocupadas
// Utiliza reduce para transformar el array de reservas en un objeto donde las claves son las fechas
// Ejemplo: { "2023-10-01": ["08:00", "09:00"], "2023-10-02": ["10:00"] }
const reservasByDate = computed(() => {
    return props.reservas.reduce((acc, reserva) => {
        if (!acc[reserva.fecha]) acc[reserva.fecha] = [];
        acc[reserva.fecha].push(reserva.hora_inicio);
        return acc;
    }, {});
});

// Computed para verificar si hay fecha y hora seleccionadas
// Necesita fecha, hora y que no esté procesando.
const canConfirmReservation = computed(() => {
    return selectedDate.value && selectedSlot.value && !form.processing;
});


//Funciones Auxiliares para formatear fechas y horas
// Función para obtener el nombre del día en español
const getDayName = (dateStr) => {
    return new Date(dateStr).toLocaleDateString("es-ES", {
        weekday: "long",
    }).toLowerCase();
};

// Función para verificar si una fecha-hora ya pasó
const isSlotExpired = (dateStr, timeStr) => {
    const slotDateTime = new Date(`${dateStr}T${timeStr}`);
    return slotDateTime <= new Date();
};

// Enviar reserva con manejo de errores 
const submitReservation = async () => {
    if (!canConfirmReservation.value) return;
    
    try {
        isLoading.value = true;
        form.fecha = selectedDate.value;
        form.hora_inicio = selectedSlot.value;
        
        await form.post(route("reservas.store"), {
            onSuccess: () => {
                // Limpiar selecciones después del éxito
                resetSelections();
            },
            onError: (errors) => {
                console.error('Error al crear reserva:', errors);
            }
        });
    } catch (error) {
        console.error('Error inesperado:', error);
    } finally {
        isLoading.value = false;
    }
};

// Función para resetear las selecciones
const resetSelections = () => {
    selectedDate.value = "";
    selectedSlot.value = "";
    availableSlots.value = [];
};

// Acción al seleccionar un día en el calendario
// Maneja el clic en una fecha del calendario, actualiza la fecha seleccionada y obtiene los horarios disponibles
const handleDateClick = async (arg) => {
    selectedDate.value = arg.dateStr;
    selectedSlot.value = "";

    const dayName = getDayName(arg.dateStr);
    const possibleSlots = slotsByDay.value[dayName] || [];
    const occupiedSlots = reservasByDate.value[selectedDate.value] || [];

    // Filtrar slots disponibles (no ocupados y no vencidos)
    availableSlots.value = possibleSlots.filter((slot) => {
        return !occupiedSlots.includes(slot) && 
               !isSlotExpired(selectedDate.value, slot);
    });

    await nextTick(); // Espera a que Vue actualice el DOM

    // Forzar re-render del calendario para actualizar los estilos
    if (calendarRef.value) {
        calendarRef.value.getApi().render();
    }
};

// Verificar si una hora está reservada
const isSlotTaken = (slot) => {
    const occupiedSlots = reservasByDate.value[selectedDate.value] || [];
    return occupiedSlots.includes(slot);
};

// Selección de hora con validación
const selectTimeSlot = (slot) => {
    if (!isSlotTaken(slot) && !isSlotExpired(selectedDate.value, slot)) {
        selectedSlot.value = slot;
    }
};

// Constantes para los colores del calendario
const CALENDAR_COLORS = {
    AVAILABLE: "#BBF7D0",      // Verde: disponible
    PARTIAL: "#FCD34D",        // Amarillo: parcialmente ocupado
    OCCUPIED: "#FECACA",       // Rojo: ocupado o vencido
    UNAVAILABLE: "#E5E7EB"     // Gris: no disponible
};

// Función para determinar el color de un día específico
const getDayColor = (dateStr) => {
    const dayName = getDayName(dateStr);
    const possibleSlots = slotsByDay.value[dayName] || [];
    
    if (possibleSlots.length === 0) {
        return CALENDAR_COLORS.UNAVAILABLE;
    }

    const occupiedSlots = reservasByDate.value[dateStr] || [];
    const availableFutureSlots = possibleSlots.filter(slot => 
        !isSlotExpired(dateStr, slot)
    );

    if (availableFutureSlots.length === 0 || occupiedSlots.length === possibleSlots.length) {
        return CALENDAR_COLORS.OCCUPIED;
    }
    
    if (occupiedSlots.length > 0) {
        return CALENDAR_COLORS.PARTIAL;
    }
    
    return CALENDAR_COLORS.AVAILABLE;
};

// Generar eventos de color optimizado
const generateColorEvents = () => {
    const events = [];
    const today = new Date();
    const daysToGenerate = 90; // Reducido de 90 días para mejor rendimiento

    for (let i = 0; i < daysToGenerate; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);
        const dateStr = date.toISOString().split("T")[0];

        events.push({
            start: dateStr,
            display: "background",
            color: getDayColor(dateStr),
        });
    }

    return events;
};

// Opciones del calendario 
const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    locale: esLocale,
    dateClick: handleDateClick,
    selectable: true,
    validRange: {
        start: new Date().toISOString().split("T")[0], // Desde hoy
    },
    height: "auto", // Altura automática para adaptarse al contenido
    fixedWeekCount: false, // Permitir semanas parciales
    events: generateColorEvents(),
    headerToolbar: {
        left: 'prev,next', // Botones de navegación
        center: 'title',
        right: '', // No mostrar el botón de hoy
    },
    buttonText: {
        today: 'Hoy'
    },

    // Personalización de celdas del calendario
    dayCellClassNames: ({ date }) => {
        // Formatear la fecha de FullCalendar al mismo formato que selectedDate
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${day}`;        const isSelected = selectedDate.value === dateStr;
        // const today = new Date();
        // const isToday = date.toDateString() === today.toDateString();
        return [
            //"rounded-lg",
            "overflow-hidden",
            "text-center",
            "cursor-pointer",
            "transition-all",
            "duration-200",
            "hover:shadow-md",
            isSelected ? "selected-date" : ""
           

        ];
    },
    dayMaxEventRows: true,// Permitir múltiples eventos por día
    aspectRatio: 1.35,// Ajustar la proporción del calendario
}));

// Formatear tiempo para mostrar
const formatTime = (timeStr) => {
    return timeStr.substring(0, 5);
};

// Formatear fecha para mostrar
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString("es-ES", {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Inicialización del componente
onMounted(() => {
    // Cualquier inicialización adicional si es necesaria
});
</script>

<template>
    <Head :title="`Reservar ${zona.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reservar Zona: {{ zona.nombre }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 text-gray-900">
                        <!-- Información de la zona -->
                        
                        <div class="mb-8 p-4 bg-gray-50 rounded-lg">

                            <h3 class="text-lg font-semibold text-blue-900 mb-2">
                                {{ zona.nombre }}
                            </h3>
                            <p class="text-blue-700 text-sm">
                                Selecciona un día disponible y elige tu horario preferido
                            </p>
                        </div>

                        <div class="lg:flex lg:space-x-8">
                            <!-- CALENDARIO -->
                            <div class="w-full lg:w-1/2 mb-8 lg:mb-0">
                                
                                
                                <div class="rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                    <FullCalendar ref="calendarRef" :options="calendarOptions" />
                                </div>

                                <!-- LEYENDA DE COLORES -->
                                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Leyenda:</h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="flex items-center">
                                            <span class="inline-block w-4 h-4 mr-2 rounded" 
                                                  :style="{ backgroundColor: CALENDAR_COLORS.AVAILABLE }"></span>
                                            <span>Disponible</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-block w-4 h-4 mr-2 rounded" 
                                                  :style="{ backgroundColor: CALENDAR_COLORS.PARTIAL }"></span>
                                            <span>Parcialmente ocupado</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-block w-4 h-4 mr-2 rounded" 
                                                  :style="{ backgroundColor: CALENDAR_COLORS.OCCUPIED }"></span>
                                            <span>Ocupado/vencido</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-block w-4 h-4 mr-2 rounded" 
                                                  :style="{ backgroundColor: CALENDAR_COLORS.UNAVAILABLE }"></span>
                                            <span>No disponible</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- HORAS DISPONIBLES Y CONFIRMACIÓN -->
                            <div class="w-full lg:w-1/2">
                                <!-- Panel de selección de hora -->
                                <div v-if="selectedDate" class="mb-8">
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-semibold">Fecha seleccionada:</span> 
                                            {{ formatDate(selectedDate) }}
                                        </p>
                                    </div>

                                    <!-- Slots disponibles -->
                                    <div v-if="availableSlots.length > 0" 
                                         class="grid grid-cols-3 sm:grid-cols-4 gap-3"> <!-- Muestra los botones de horarios disponibles -->
                                        <button
                                            v-for="slot in availableSlots"
                                            :key="slot"
                                            @click="selectTimeSlot(slot)"
                                            class="p-3 border-2 rounded-lg text-center font-semibold transition-all duration-200 transform hover:scale-105"
                                            :disabled="isSlotTaken(slot)"
                                            :class="{
                                                'bg-blue-600 text-white border-blue-600 shadow-lg': selectedSlot === slot,
                                                'bg-red-100 text-red-600 border-red-300 cursor-not-allowed opacity-50': isSlotTaken(slot),
                                                'bg-white hover:bg-gray-50 border-gray-300 hover:border-gray-400 text-gray-700': !isSlotTaken(slot) && selectedSlot !== slot,
                                            }"
                                        >
                                            {{ formatTime(slot) }}
                                        </button>
                                    </div>
                                    
                                    <!-- Sin horarios disponibles -->
                                    <div v-else class="p-6 bg-yellow-50 text-yellow-800 rounded-lg">
                                        <div class="flex items-center">
                                            
                                            <span class="font-medium">No hay horarios disponibles para este día</span>
                                        </div>
                                    </div>
                                </div>

                               
                                <!-- CONFIRMACIÓN DE RESERVA -->
                                <div v-if="selectedSlot" class="pt-6 ">
                                    
                                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-blue-900 mb-3">Resumen de la reserva:</h4>
                                            <div class="space-y-2 text-sm">
                                                <p class="flex justify-between">
                                                    <span class="text-blue-700">Zona:</span>
                                                    <span class="font-semibold text-blue-900">{{ zona.nombre }}</span>
                                                </p>
                                                <p class="flex justify-between">
                                                    <span class="text-blue-700">Fecha:</span>
                                                    <span class="font-semibold text-blue-900">{{ formatDate(selectedDate) }}</span>
                                                </p>
                                                <p class="flex justify-between">
                                                    <span class="text-blue-700">Hora:</span>
                                                    <span class="font-semibold text-blue-900">{{ formatTime(selectedSlot) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-3">
                                            <button
                                                @click="submitReservation"
                                                :disabled="!canConfirmReservation || isLoading"
                                                class="flex-1 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                            >
                                                <span v-if="isLoading" class="flex items-center justify-center">
                                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Procesando...
                                                </span>
                                                <span v-else>Confirmar Reserva</span>
                                            </button>
                                            
                                            <button
                                                @click="resetSelections"
                                                class="px-4 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>

/* Resaltar el día seleccionado */
:deep(.selected-date .fc-daygrid-day-frame) {
    border: 2px solid #3B82F6 !important;
    border-radius: 6px !important;
    margin: 1px !important;
    background-color: rgba(59, 130, 246, 0.1) !important;
}

:deep(.selected-date .fc-daygrid-day-number) {
    color: #1D4ED8 !important;
    font-weight: 600 !important;
}

/* Estilizar las flechas de navegación del calendario */
:deep(.fc-prev-button),
:deep(.fc-next-button) {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    padding: 8px !important;
    margin: 0 4px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

:deep(.fc-prev-button:hover),
:deep(.fc-next-button:hover) {
    background-color: #F3F4F6 !important;
    transform: scale(1.05) !important;
}

:deep(.fc-prev-button:focus),
:deep(.fc-next-button:focus) {
    background-color: #E5E7EB !important;
    outline: none !important;
}

:deep(.fc-prev-button .fc-icon),
:deep(.fc-next-button .fc-icon) {
    color: #000000 !important;
    font-size: 16px !important;
    font-weight: 900 !important;
}

:deep(.fc-prev-button:hover .fc-icon),
:deep(.fc-next-button:hover .fc-icon) {
    color: #000000 !important;
}

:deep(.fc-header-toolbar) {
  margin: 0 !important;
  padding: 10px;
}

</style>