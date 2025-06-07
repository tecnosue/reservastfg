<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, nextTick } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";

// Props que llegan desde el backend
const props = defineProps({
    zona: Object,
    reservas: Array,
    disponibilidades: Array,
});

// Variables reactivas para el estado de la reserva
const selectedDate = ref("");
const availableSlots = ref([]);
const selectedSlot = ref("");

// Formulario de reserva
const form = useForm({
    zona_id: props.zona.id,
    fecha: "",
    hora_inicio: "",
});

// Enviar reserva
const submitReservation = () => {
    form.fecha = selectedDate.value;
    form.hora_inicio = selectedSlot.value;
    form.post(route("reservas.store"));
};

// Agrupar horarios disponibles por día de la semana
const allSlotsByDay = props.disponibilidades.reduce((acc, slot) => {
    const dia = slot.dia_semana.toLowerCase();
    if (!acc[dia]) acc[dia] = [];
    acc[dia].push(slot.hora_inicio);
    return acc;
}, {});

// Acción al seleccionar un día
const handleDateClick = async (arg) => {
    selectedDate.value = arg.dateStr;
    selectedSlot.value = "";

    const diaSemana = new Date(arg.dateStr).toLocaleDateString("es-ES", {
        weekday: "long",
    }).toLowerCase();

    const posibles = allSlotsByDay[diaSemana] || [];
    const ocupadas = props.reservas.filter(r => r.fecha === selectedDate.value).map(r => r.hora_inicio);
    const ahora = new Date();

    // Filtrar slots que no estén reservados ni vencidos
    availableSlots.value = posibles.filter((slot) => {
        const fechaSlot = new Date(`${selectedDate.value}T${slot}`);
        return fechaSlot > ahora && !ocupadas.includes(slot);
    });

    await nextTick(); // fuerza redibujado para evitar errores visuales
};

// Saber si una hora está ya reservada
const isSlotTaken = (slot) => {
    return props.reservas.some(
        (reserva) => reserva.fecha === selectedDate.value && reserva.hora_inicio === slot
    );
};

// Selección de hora
const selectTimeSlot = (slot) => {
    if (!isSlotTaken(slot)) {
        selectedSlot.value = slot;
    }
};

// Generar eventos de color de fondo por día
const generarEventosColor = () => {
    const eventos = [];
    const hoy = new Date();

    for (let i = 0; i < 60; i++) {
        const fecha = new Date();
        fecha.setDate(hoy.getDate() + i);
        const fechaStr = fecha.toISOString().split("T")[0];
        const diaNombre = fecha.toLocaleDateString("es-ES", { weekday: "long" }).toLowerCase();

        const posibles = allSlotsByDay[diaNombre] || [];
        const ocupadas = props.reservas.filter((r) => r.fecha === fechaStr);

        let color = "#E5E7EB"; // gris por defecto

        if (posibles.length > 0) {
            const ahora = new Date();
            const futurasDisponibles = posibles.some((h) => new Date(`${fechaStr}T${h}`) > ahora);
            const total = posibles.length;
            const reservadas = ocupadas.length;

            if (!futurasDisponibles || reservadas === total) {
                color = "#FECACA"; // rojo: todo ocupado o vencido
            } else if (reservadas > 0) {
                color = "#FCD34D"; // naranja: parcialmente ocupado
            } else {
                color = "#BBF7D0"; // verde: todo disponible
            }
        }

        eventos.push({
            start: fechaStr,
            display: "background",
            color,
        });
    }

    return eventos;
};

// Opciones del calendario
const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    locale: esLocale,
    dateClick: handleDateClick,
    selectable: true,
    validRange: {
        start: new Date().toISOString().split("T")[0],
    },
    height: "auto",
    fixedWeekCount: true,
    events: generarEventosColor(),
    dayCellClassNames: () => ["rounded-lg", "overflow-hidden"], // bordes redondeados para el calendario
});
</script>

<template>
    <Head :title="'Reservar ' + zona.nombre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reservar Zona: {{ zona.nombre }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="md:flex md:space-x-8">
                            <!-- CALENDARIO -->
                            <div class="w-full md:w-1/2">
                                <h3 class="text-lg font-bold mb-4">1. Selecciona un día</h3>
                                <FullCalendar :options="calendarOptions" />

                                <!-- LEYENDA DE COLORES -->
                                <div class="mt-6 text-sm">
                                    <p><span class="inline-block w-4 h-4 mr-2 bg-[#BBF7D0]"></span> Disponible</p>
                                    <p><span class="inline-block w-4 h-4 mr-2 bg-[#FCD34D]"></span> Parcialmente ocupado</p>
                                    <p><span class="inline-block w-4 h-4 mr-2 bg-[#FECACA]"></span> Ocupado o vencido</p>
                                    <p><span class="inline-block w-4 h-4 mr-2 bg-[#E5E7EB]"></span> No disponible</p>
                                </div>
                            </div>

                            <!-- HORAS DISPONIBLES -->
                            <div v-if="selectedDate" class="w-full md:w-1/2 mt-8 md:mt-0">
                                <h3 class="text-lg font-bold mb-4">
                                    2. Selecciona una hora para el día {{ selectedDate }}
                                </h3>
                                <div v-if="availableSlots.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-4">
                                    <button
                                        v-for="slot in availableSlots"
                                        :key="slot"
                                        @click="selectTimeSlot(slot)"
                                        class="p-2 border rounded-lg text-center font-semibold"
                                        :disabled="isSlotTaken(slot)"
                                        :class="{
                                            'bg-indigo-600 text-white': selectedSlot === slot,
                                            'bg-red-200 text-red-800 cursor-not-allowed': isSlotTaken(slot),
                                            'bg-gray-100 hover:bg-gray-200': !isSlotTaken(slot) && selectedSlot !== slot,
                                        }"
                                    >
                                        {{ slot.substring(0, 5) }}
                                    </button>
                                </div>
                                <div v-else class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                                    No hay horas disponibles para este día.
                                </div>

                                <!-- CONFIRMACIÓN -->
                                <div v-if="selectedSlot" class="mt-8 pt-6 border-t">
                                    <h3 class="text-lg font-bold mb-4">3. Confirma tu reserva</h3>
                                    <div class="p-4 bg-blue-100 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div>
                                            <p><span class="font-semibold">Zona:</span> {{ zona.nombre }}</p>
                                            <p><span class="font-semibold">Día:</span> {{ selectedDate }}</p>
                                            <p><span class="font-semibold">Hora:</span> {{ selectedSlot.substring(0, 5) }}</p>
                                        </div>
                                        <button
                                            @click="submitReservation"
                                            :disabled="form.processing"
                                            class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 disabled:opacity-50"
                                        >
                                            Confirmar Reserva
                                        </button>
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
