<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

// Definimos las 'props' que este componente recibe desde el controlador
defineProps({
    zonas: Array,
});
</script>

<template>
    <Head title="Panel de Administrador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Panel de Administrador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">
                            ¡Bienvenido, Administrador!
                        </h3>
                        <p class="mb-6">
                            Desde aquí puedes gestionar el sistema de reservas.
                        </p>

                        <!-- botones -->
                        <div class="flex space-x-4">
                            <Link
                                :href="route('zonas.index')"
                                class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700"
                            >
                                Gestionar Zonas
                            </Link>
                            <Link
                                :href="route('zonas.create')"
                                class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-700"
                            >
                                Crear Nueva Zona
                            </Link>
                            <Link
                                href="/usuarios/pendientes"
                                class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-700"
                            >
                                Ver Usuarios Pendientes
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
