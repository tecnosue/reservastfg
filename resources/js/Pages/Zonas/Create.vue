<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// 'useForm' es un ayudante de Inertia para gestionar formularios.
// Inicializamos nuestro formulario con los campos que necesitamos.
const form = useForm({
    nombre: '',
    descripcion: '',
    imagen: null,
});

// Esta función enviará el formulario al backend cuando se haga submit.
const submit = () => {
    // .post envía los datos a la ruta 'zonas.store' que creamos antes.
    form.post(route('zonas.store'));
};
</script>

<template>
    <Head title="Crear Nueva Zona" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nueva Zona
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" id="nombre" v-model="form.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <div v-if="form.errors.nombre" class="text-sm text-red-600 mt-1">{{ form.errors.nombre }}</div>
                            </div>

                            <div class="mb-4">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="descripcion" v-model="form.descripcion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                <div v-if="form.errors.descripcion" class="text-sm text-red-600 mt-1">{{ form.errors.descripcion }}</div>
                            </div>

                            <div class="mb-4">
                                <label for="imagen" class="block text-sm font-medium text-gray-700">Imagen</label>
                                <input type="file" id="imagen" @input="form.imagen = $event.target.files[0]" class="mt-1 block w-full">
                                <div v-if="form.errors.imagen" class="text-sm text-red-600 mt-1">{{ form.errors.imagen }}</div>
                            </div>
                            
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-500   text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                                    Guardar Zona
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>