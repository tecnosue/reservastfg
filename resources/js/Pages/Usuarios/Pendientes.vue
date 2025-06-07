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

            <table v-else class="table-auto w-full mt-4">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Grupo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="usuario in usuarios" :key="usuario.id">
                        <td>{{ usuario.name }}</td>
                        <td>{{ usuario.email }}</td>
                        <td>
                            <select
                                v-model="forms[usuario.id].grupo_id"
                                class="border rounded"
                            >
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
                        <td>
                            <button
                            @click="forms[usuario.id].put(route('usuarios.activar', usuario.id))"
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
