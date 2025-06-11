<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link  } from "@inertiajs/vue3";
import PendingUsersTable from '@/Components/Admin/PendingUsersTable.vue';
import { defineProps } from 'vue'; // Si no está ya importado


// Definimos las 'props' que este componente recibe desde el controlador
defineProps({
    zonas: Array,
    usuariosPendientes: Array,
    grupos: Array,
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">
                            ¡Bienvenido, Administrador!
                        </h3>
                        <p class="mb-6">
                            Desde aquí puedes gestionar el sistema de reservas.
                        </p>

                        <!-- botones -->
                        <div class="
                                flex
                                flex-col        /* Por defecto (móvil): los elementos se apilan verticalmente */
                                sm:flex-row     /* A partir de 'sm' (640px): los elementos se disponen en fila */
                                space-y-4       /* Margen vertical entre elementos para móvil */
                                sm:space-y-0    /* Elimina el margen vertical en pantallas 'sm' y superiores */
                                sm:space-x-4    /* Margen horizontal entre elementos para pantallas 'sm' y superiores */
                                ">
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
                             <Link
                                :href="route('grupos.index')"
                                class="px-4 py-2 bg-purple-500 text-white font-semibold rounded-lg shadow-md hover:bg-purple-700"
                            >
                                Gestionar Grupos
                            </Link>
                        </div>
                         </div>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Sección de usuarios pendientes -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold mb-4">Usuarios Pendientes de Activación</h3>
                            <PendingUsersTable 
                                v-if="usuariosPendientes.length > 0"
                                :usuarios="usuariosPendientes"
                                :grupos="grupos"
                            />
                            <p v-else class="text-gray-500">No hay usuarios pendientes de activación</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </AuthenticatedLayout>
</template>
