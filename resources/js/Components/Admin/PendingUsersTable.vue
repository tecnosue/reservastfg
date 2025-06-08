<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';



const props = defineProps({
    usuarios: Array,
    grupos: Array,
});

const forms = {};

// Inicializar formularios para cada usuario
props.usuarios.forEach(usuario => {
    forms[usuario.id] = useForm({
        grupo_id: '',
    });
});

const activarUsuario = (usuarioId) => {
    forms[usuarioId].put(route('usuarios.activar', usuarioId), {
        preserveScroll: true,
        onSuccess: () => {
            // Eliminar usuario de la lista localmente
            const index = props.usuarios.findIndex(u => u.id === usuarioId);
            if (index !== -1) {
                props.usuarios.splice(index, 1);
            }
        }
    });
};


</script>

<template>
    <div class="mt-8">
        
        
        <div v-if="usuarios.length === 0" class="p-4 text-center text-gray-500">
            No hay usuarios pendientes de activación.
        </div>
        
        <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Grupo</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="usuario in usuarios" :key="usuario.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ usuario.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ usuario.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <select v-model="forms[usuario.id].grupo_id" class="border border-gray-300 rounded p-1">
                            <option value="" disabled>Selecciona un grupo</option>
                            <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                                {{ grupo.nombre }}
                            </option>
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <button
                            @click="activarUsuario(usuario.id)"
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
</template>
<style scoped>
.fixed {
    animation: slideIn 0.5s forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>