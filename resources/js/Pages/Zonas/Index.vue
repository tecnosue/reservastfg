<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// El controlador le pasa una 'prop' llamada 'zonas' a esta vista
defineProps({
    zonas: Array
});
</script>

<template>
    <Head title="Gestión de Zonas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestión de Zonas Comunes
                </h2>
                <Link :href="route('zonas.create')" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-700">
                    + Crear Zona
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--comprueba si el objeto $page.props.flash existe y si tiene la propiedad message -->
                <div v-if="$page.props.flash && $page.props.flash.message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.message }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div v-for="zona in zonas" :key="zona.id" class="flex items-center space-x-4 border-b last:border-b-0 pb-4">
                            
                            <img 
                                :src="'/storage/' + zona.imagen" 
                                alt="Imagen de la zona" 
                                class="w-32 h-20 object-cover rounded-lg"
                            >

                            <div class="flex-grow">
                                <h3 class="text-lg font-bold">{{ zona.nombre }}</h3>
                                <p class="text-gray-600">{{ zona.descripcion }}</p>
                            </div>

                            <div>
                                </div>
                        </div>

                        <div v-if="zonas.length === 0">
                            <p>No hay zonas creadas todavía. ¡Crea la primera!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>