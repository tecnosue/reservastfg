<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    reservas: Object, // Ahora es un objeto por la paginación
});

const showConfirmation = () => {
    return window.confirm(
        "¿Estás seguro de que quieres cancelar esta reserva?"
    );
};
</script>

<template>
    <Head title="Mis Reservas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Reservas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div v-if="reservas.data.length > 0">
                            <div
                                v-for="reserva in reservas.data"
                                :key="reserva.id"
                                class="flex items-center justify-between p-4 border rounded-lg"
                            >
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">
                                        {{ reserva.zona.nombre }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Día: {{ reserva.fecha }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Hora:
                                        {{
                                            reserva.hora_inicio.substring(0, 5)
                                        }}
                                        - {{ reserva.hora_fin.substring(0, 5) }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="text-sm font-semibold px-3 py-1 rounded-full"
                                        :class="{
                                            'bg-green-200 text-green-800':
                                                reserva.estado === 'activa',
                                            'bg-red-200 text-red-800':
                                                reserva.estado === 'cancelada',
                                        }"
                                    >
                                        {{ reserva.estado }}
                                    </div>

                                    <Link
                                        v-if="reserva.estado === 'activa'"
                                        :href="
                                            route(
                                                'reservas.destroy',
                                                reserva.id
                                            )
                                        "
                                        method="delete"
                                        as="button"
                                        class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700"
                                        :onBefore="showConfirmation"
                                    >
                                        Cancelar
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-8">
                            <p>Aún no has realizado ninguna reserva.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
