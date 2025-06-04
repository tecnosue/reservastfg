<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    zona: Object, // La zona que estamos editando
    errors: Object, // Errores de validación si los hay
});

const form = useForm({
    _method: 'PUT', // Para simular una petición PUT con un POST si usamos form.post
    nombre: props.zona.nombre,
    descripcion: props.zona.descripcion,
    imagen: null, // Para la nueva imagen, si se sube
});

const currentImageUrl = ref(props.zona.imagen ? `/storage/${props.zona.imagen}` : null);
const newImagePreview = ref(null);

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        form.imagen = file; // El objeto File se asigna a form.imagen
        const reader = new FileReader();
        reader.onload = (e) => {
            newImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.imagen = null;
        newImagePreview.value = null;
    }
}

const submit = () => {
    // Usamos form.post porque Inertia maneja la subida de archivos y _method='PUT'
    // para que Laravel lo trate como una petición PUT.
    form.post(route('zonas.update', props.zona.id), {
        // No es necesario onSuccess aquí si la redirección del controlador es suficiente.
        // onError: () => { /* Manejar errores si es necesario */ }
    });
};
</script>

<template>
    <Head :title="'Editar Zona: ' + zona.nombre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Zona: {{ zona.nombre }}
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

                            

                            <div v-if="currentImageUrl && !newImagePreview" class="mb-4">
                                <p class="block text-sm font-medium text-gray-700">Imagen Actual:</p>
                                <img :src="currentImageUrl" alt="Imagen actual" class="mt-1 w-48 h-auto rounded-md border border-gray-300">
                            </div>
                            <div v-if="newImagePreview" class="mb-4">
                                <p class="block text-sm font-medium text-gray-700">Nueva Imagen (previsualización):</p>
                                <img :src="newImagePreview" alt="Previsualización nueva imagen" class="mt-1 w-48 h-auto rounded-md border border-gray-300">
                            </div>
                            <div class="mb-4">
                                <label for="imagen" class="block text-sm font-medium text-gray-700">Cambiar Imagen (opcional)</label>
                                <input type="file" id="imagen" @input="handleImageUpload" class="mt-1 block w-full">
                                <div v-if="form.errors.imagen" class="text-sm text-red-600 mt-1">{{ form.errors.imagen }}</div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('zonas.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 disabled:opacity-50">
                                    Actualizar Zona
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>