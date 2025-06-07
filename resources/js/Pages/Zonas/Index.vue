<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3"; // Importa el router de Inertia


// Función para eliminar una zona
function eliminarZona(zonaId) {
    if (window.confirm("¿Seguro que quieres eliminar esta zona?")) {
        router.delete(route("zonas.destroy", zonaId));
    }
}

// El controlador le pasa una 'prop' llamada 'zonas' a esta vista
defineProps({
    zonas: Array,
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
                
            </div>
        </template>
        <template #action>
            <Link
                href="/zonas/create"
                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700"
            >
                + Crear Zona
            </Link>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div
                            v-for="zona in zonas"
                            :key="zona.id"
                            class="flex items-center space-x-4 border-b last:border-b-0 pb-4"
                        >
                            <img
                                :src="'/storage/' + zona.imagen"
                                alt="Imagen de la zona"
                                class="w-32 h-20 object-cover rounded-lg"
                            />

                            <div class="flex-grow">
                                <h3 class="text-lg font-bold">
                                    {{ zona.nombre }}
                                </h3>
                                <p class="text-gray-600">
                                    {{ zona.descripcion }}
                                </p>
                            </div>
                            <div
                                class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 items-center"
                            >
                                <Link
                                    :href="
                                        route('reservas.create', {
                                            zona: zona.id,
                                        })
                                    "
                                    class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-lg hover:bg-blue-700 text-center w-full sm:w-auto"
                                >
                                    Reservar
                                </Link>
                                <Link
                                    :href="route('zonas.edit', zona.id)"
                                    class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-lg hover:bg-yellow-700 text-center w-full sm:w-auto"
                                >
                                    Editar
                                </Link>

                                <button
                                    @click="eliminarZona(zona.id)"
                                    class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-lg hover:bg-red-700 text-center w-full sm:w-auto"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>

                        <div v-if="zonas.length === 0">
                            <p>
                                No hay zonas creadas todavía. ¡Crea la primera!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
