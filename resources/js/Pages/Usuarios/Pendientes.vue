<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    usuarios: Array,
    grupos: Array,
});
const forms = props.usuarios.reduce((acc, user) => {
    acc[user.id] = useForm({
        grupo_id: "",
    });
    return acc;
}, {});
</script>

<template>
    <Head title="Usuarios Pendientes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios Pendientes de Activación
            </h2>
        </template>
        <div>
            <div
                v-if="usuarios.length === 0"
                class="p-4 text-center text-gray-500"
            >
                No hay usuarios pendientes de activación.
            </div>
            <table v-else class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-100">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-sm font-medium text-gray-700"
                        >
                            Nombre
                        </th>
                        <th
                            class="px-6 py-3 text-left text-sm font-medium text-gray-700"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-3 text-left text-sm font-medium text-gray-700"
                        >
                            Grupo
                        </th>
                        <th
                            class="px-6 py-3 text-left text-sm font-medium text-gray-700"
                        >
                            Acción
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="usuario in usuarios" :key="usuario.id">
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            {{ usuario.name }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            {{ usuario.email }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            <select
                                v-model="forms[usuario.id].grupo_id"
                                class="border border-gray-300 rounded p-1"
                            >
                                <option disabled value="">
                                    Selecciona un grupo
                                </option>
                                <option
                                    v-for="grupo in grupos"
                                    :value="grupo.id"
                                    :key="grupo.id"
                                >
                                    {{ grupo.nombre }}
                                </option>
                            </select>
                            <div
                                v-if="forms[usuario.id].errors.grupo_id"
                                class="text-red-600 text-sm mt-1"
                            >
                                {{ forms[usuario.id].errors.grupo_id }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            <button
                                @click="
                                    forms[usuario.id].put(
                                        route('usuarios.activar', usuario.id)
                                    )
                                "
                                :disabled="!forms[usuario.id].grupo_id"
                                class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 disabled:opacity-50"
                            >
                                Activar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
