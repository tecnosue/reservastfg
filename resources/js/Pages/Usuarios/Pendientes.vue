<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    usuarios: Array
});

const activarUsuario = (id) => {
    router.put(`/usuarios/${id}/activar`);
};
</script>

<template>
    <Head title="Usuarios Pendientes" />
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Usuarios pendientes de activación</h1>
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="usuario in usuarios" :key="usuario.id">
                    <td class="border px-4 py-2">{{ usuario.name }}</td>
                    <td class="border px-4 py-2">{{ usuario.email }}</td>
                    <td class="border px-4 py-2">
                        <button
                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                            @click="activarUsuario(usuario.id)"
                        >
                            Activar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
