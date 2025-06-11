<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PendingUsersTable from "@/Components/Admin/PendingUsersTable.vue";

const props = defineProps({
    usuarios: Array, // Esta prop contiene los usuarios que vienen del controlador para esta página.
    grupos: Array, // Esta prop contiene los grupos.
});


</script>

<template>
    <AuthenticatedLayout>
               
        <template #header>
                       
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Usuarios Pendientes de Activación            
            </h2>
                   
        </template>

               
        <div class="py-12">
                       
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                               
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                       
                    <div class="p-6 text-gray-900">
                                                                       
                        <div class="mb-8">
                                                       
                            
                                                                               
                               
                            <PendingUsersTable
                                v-if="props.usuarios.length > 0"
                                :usuarios="props.usuarios"
                                :grupos="props.grupos"
                            />
                                                       
                            <p v-else class="text-gray-500">
                                No hay usuarios pendientes de activación
                            </p>
                                                   
                        </div>
                                           
                    </div>
                                   
                </div>
                           
            </div>
                   
        </div>
           
    </AuthenticatedLayout>
</template>
