<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    grupos: Array,
});

const form = useForm({
    nombre: "",
});
const deleteGroup = (groupId) => {
    if (confirm("¿Eliminar este grupo?")) {
        $inertia.delete(route("grupos.destroy", groupId));
    }
};

const submitForm = () => {
    form.post(route("grupos.store"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Gestión de Grupos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Grupos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <h3 class="text-lg font-medium mb-4">Crear Nuevo Grupo</h3>
                    <form @submit.prevent="submitForm" class="mb-8">
                        <div class="flex space-x-4">
                            <input
                                type="text"
                                v-model="form.nombre"
                                placeholder="Nombre del grupo"
                                class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            />
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                :disabled="form.processing"
                            >
                                Crear
                            </button>
                        </div>
                        <p
                            v-if="form.errors.nombre"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ form.errors.nombre }}
                        </p>
                    </form>

                    <h3 class="text-lg font-medium mb-4">Lista de Grupos</h3>
                    <div v-if="grupos.length > 0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Nombre
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="grupo in grupos" :key="grupo.id">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    >
                                        {{ grupo.id }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    >
                                        {{ grupo.nombre }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        <button
                                            @click="deleteGroup(grupo.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center text-gray-500">
                        No hay grupos registrados
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
