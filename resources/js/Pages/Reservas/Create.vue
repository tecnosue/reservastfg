<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

// Importaciones de FullCalendar
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";

const props = defineProps({
    zona: Object,
    reservas: Array, // Recibimos las reservas existentes como prop
});

// -- ESTADO DEL COMPONENTE --
const selectedDate = ref("");
const availableSlots = ref([]);
const selectedSlot = ref("");

// Definimos todos los posibles tramos horarios para una zona (de 1h de duración)
const allDaySlots = [
    "09:00:00",
    "10:00:00",
    "11:00:00",
    "12:00:00",
    "13:00:00",
    "14:00:00",
    "15:00:00",
    "16:00:00",
    "17:00:00",
    "18:00:00",
    "19:00:00",
    "20:00:00",
    "21:00:00",
    "22:00:00",
];

// -- LÓGICA DEL FORMULARIO DE RESERVA FINAL --
// Usamos useForm para el envío final de la reserva
const form = useForm({
    zona_id: props.zona.id,
    fecha: "",
    hora_inicio: "",
});

const submitReservation = () => {
    form.fecha = selectedDate.value;
    form.hora_inicio = selectedSlot.value;
    form.post(route("reservas.store"));
};

// -- LÓGICA DEL CALENDARIO Y LAS HORAS --
const handleDateClick = (arg) => {
    selectedDate.value = arg.dateStr;
    selectedSlot.value = ""; // Reseteamos la hora seleccionada si se cambia de día

    // Filtramos las reservas para obtener solo las del día seleccionado
    const todaysBookings = props.reservas
        .filter((reserva) => reserva.fecha === selectedDate.value)
        .map((reserva) => reserva.hora_inicio);

    // Comparamos todos los horarios con los ya reservados y nos quedamos con los libres
    availableSlots.value = allDaySlots.filter(
        (slot) => !todaysBookings.includes(slot)
    );
};

const selectTimeSlot = (slot) => {
    selectedSlot.value = slot; // Guardamos la hora que el usuario ha elegido
};

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    locale: esLocale,
    dateClick: handleDateClick,
    selectable: true,
    validRange: {
        start: new Date().toISOString().split("T")[0],
    },
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
                                <h3 class="text-lg font-bold mb-4">
                                    1. Selecciona un día
                                </h3>
                                <FullCalendar :options="calendarOptions" />
                            </div>

                            <!-- HORAS DISPONIBLES -->
                            <div
                                v-if="selectedDate"
                                class="w-full md:w-1/2 mt-8 md:mt-0"
                            >
                                <h3 class="text-lg font-bold mb-4">
                                    2. Selecciona una hora para el día
                                    {{ selectedDate }}
                                </h3>
                                <div
                                    v-if="availableSlots.length > 0"
                                    class="grid grid-cols-3 sm:grid-cols-4 gap-4"
                                >
                                    <button
                                        v-for="slot in availableSlots"
                                        :key="slot"
                                        @click="selectTimeSlot(slot)"
                                        class="p-2 border rounded-lg text-center font-semibold"
                                        :class="{
                                            'bg-indigo-600 text-white':
                                                selectedSlot === slot,
                                            'bg-gray-100 hover:bg-gray-200':
                                                selectedSlot !== slot,
                                        }"
                                    >
                                        {{ slot.substring(0, 5) }}
                                    </button>
                                </div>
                                <div
                                    v-else
                                    class="p-4 bg-yellow-100 text-yellow-800 rounded-lg"
                                >
                                    No hay horas disponibles para este día.
                                </div>
                                <!-- CONFIRMACIÓN -->
                                <div
                                    v-if="selectedSlot"
                                    class="mt-8 pt-6 border-t"
                                >
                                    <h3 class="text-lg font-bold mb-4">
                                        3. Confirma tu reserva
                                    </h3>
                                    <div
                                        class="p-4 bg-blue-100 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                                    >
                                        <div>
                                            <p>
                                                <span class="font-semibold"
                                                    >Zona:</span
                                                >
                                                {{ zona.nombre }}
                                            </p>
                                            <p>
                                                <span class="font-semibold"
                                                    >Día:</span
                                                >
                                                {{ selectedDate }}
                                            </p>
                                            <p>
                                                <span class="font-semibold"
                                                    >Hora:</span
                                                >
                                                {{
                                                    selectedSlot.substring(0, 5)
                                                }}
                                            </p>
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

        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <h3 class="text-lg font-bold mb-4">1. Selecciona un día</h3>
                        <FullCalendar :options="calendarOptions" />

                        <div v-if="selectedDate" class="mt-8">
                            <h3 class="text-lg font-bold mb-4">2. Selecciona una hora para el día {{ selectedDate }}</h3>
                            <div v-if="availableSlots.length > 0" class="grid grid-cols-3 md:grid-cols-5 gap-4">
                                <button
                                    v-for="slot in availableSlots"
                                    :key="slot"
                                    @click="selectTimeSlot(slot)"
                                    class="p-2 border rounded-lg text-center font-semibold"
                                    :class="{ 'bg-indigo-600 text-white': selectedSlot === slot, 'bg-gray-100 hover:bg-gray-200': selectedSlot !== slot }"
                                >
                                    {{ slot.substring(0, 5) }}
                                </button>
                            </div>
                            <div v-else class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                                No hay horas disponibles para este día.
                            </div>
                        </div>

                        <div v-if="selectedSlot" class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-bold mb-4">3. Confirma tu reserva</h3>
                            <div class="p-4 bg-blue-100 rounded-lg flex items-center justify-between">
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
        </div> -->
    </AuthenticatedLayout>
</template>
