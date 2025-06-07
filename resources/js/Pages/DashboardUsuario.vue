<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

// Definimos las 'props' que este componente recibe desde el controlador
defineProps({
    zonas: Array,
});
</script>

<template>
    <Head title="Zonas Disponibles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Zonas Comunes Disponibles
            </h2>        
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-6">
                        <div
                            v-for="zona in zonas"
                            :key="zona.id"
                            class="flex flex-col md:flex-row items-center md:space-x-6 border rounded-lg p-4"
                        >
                            <img
                                :src="'/storage/' + zona.imagen"
                                alt="Imagen de la zona"
                                class="w-full md:w-48 h-32 object-cover rounded-lg mb-4 md:mb-0"
                            />

                            <div class="flex-grow">
                                <h3 class="text-xl font-bold">
                                    {{ zona.nombre }}
                                </h3>
                                <p class="text-gray-600 mt-1">
                                    {{ zona.descripcion }}
                                </p>
                            </div>

                            <Link
                                :href="
                                    route('reservas.create', { zona: zona.id })
                                "
                                class="w-full md:w-auto text-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700"
                            >
                                Reservar
                            </Link>
                        </div>

                        <div v-if="!zonas || zonas.length === 0">
                            <p>
                                Actualmente no hay zonas comunes disponibles
                                para reservar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
