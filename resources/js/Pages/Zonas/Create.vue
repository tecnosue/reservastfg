<script setup>
import { ref } from "vue"; // Importamos 'ref' de Vue para crear variables reactivas.
// 'ref' nos permite crear una variable que Vue puede rastrear para cambios y actualizaciones.
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
// 'useForm' es un ayudante de Inertia para gestionar formularios.
// 'usePage' nos permite acceder a la página actual y sus propiedades.

import { router } from '@inertiajs/vue3';
// 'router' es el objeto de enrutamiento de Inertia que nos permite enviar solicitudes al backend.
const props = defineProps({
    grupos: Array, 
});

const diasSemana = [
    "lunes",
    "martes",
    "miercoles",
    "jueves",
    "viernes",
    "sabado",
    "domingo",
];

// Inicializamos nuestro formulario con los campos que necesitamos.
const form = useForm({
    nombre: "",
    descripcion: "",
    imagen: null,
    disponibilidad: [],
    grupo_id: "", 

});
const agregarDisponibilidad = () => {
    form.disponibilidad.push({ dia_semana: "", hora_inicio: "", hora_fin: "" });
};

const eliminarDisponibilidad = (index) => {
    form.disponibilidad.splice(index, 1);
};
// Esta función enviará el formulario al backend cuando se haga submit.
const submit = () => {
    const formData = new FormData();
    formData.append("nombre", form.nombre);
    formData.append("descripcion", form.descripcion);
    formData.append("imagen", form.imagen);

    // IMPORTANTE: serializar la disponibilidad como JSON
    formData.append("disponibilidad", JSON.stringify(form.disponibilidad));
    formData.append("grupo_id", form.grupo_id); 

    router.post(route("zonas.store"), formData, {
        forceFormData: true,
       
    });
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
                                <label
                                    for="nombre"
                                    class="block text-sm font-medium text-gray-700"
                                    >Nombre</label
                                >
                                <input
                                    type="text"
                                    id="nombre"
                                    v-model="form.nombre"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                <div
                                    v-if="form.errors.nombre"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label
                                    for="descripcion"
                                    class="block text-sm font-medium text-gray-700"
                                    >Descripción</label
                                >
                                <textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                ></textarea>
                                <div
                                    v-if="form.errors.descripcion"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.descripcion }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label
                                    for="imagen"
                                    class="block text-sm font-medium text-gray-700"
                                    >Imagen</label
                                >
                                <input
                                    type="file"
                                    id="imagen"
                                    @input="
                                        form.imagen = $event.target.files[0]
                                    "
                                    class="mt-1 block w-full"
                                />
                                <div
                                    v-if="form.errors.imagen"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.imagen }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="grupo_id" class="block text-sm font-medium text-gray-700">Grupo</label>
                                <select
                                    id="grupo_id"
                                    v-model="form.grupo_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="" disabled>Selecciona un grupo</option>
                                    <option
                                        v-for="grupo in grupos"
                                        :key="grupo.id"
                                        :value="grupo.id"
                                    >
                                        {{ grupo.nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.grupo_id"
                                    class="text-sm text-red-600 mt-1"
                                >
                                    {{ form.errors.grupo_id }}
                                </div>
                            </div>

                            <progress
                                v-if="form.progress"
                                :value="form.progress.percentage"
                                max="100"
                            >
                                {{ form.progress.percentage }}%
                            </progress>
                            <div>
                                <label class="block font-medium"
                                    >Disponibilidad</label
                                >
                                <div
                                    v-for="(slot, index) in form.disponibilidad"
                                    :key="index"
                                    class="flex items-center gap-4 mb-2"
                                >
                                    <select
                                        v-model="slot.dia_semana"
                                        class="border rounded p-1"
                                    >
                                        <option disabled value="">Día</option>
                                        <option
                                            v-for="dia in diasSemana"
                                            :key="dia"
                                            :value="dia"
                                        >
                                            {{ dia }}
                                        </option>
                                    </select>

                                    <input
                                        v-model="slot.hora_inicio"
                                        type="time"
                                        class="border rounded p-1"
                                    />
                                    <input
                                        v-model="slot.hora_fin"
                                        type="time"
                                        class="border rounded p-1"
                                    />
                                    <button
                                        type="button"
                                        @click="eliminarDisponibilidad(index)"
                                        class="text-red-500 hover:underline"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="agregarDisponibilidad"
                                    class="text-blue-600 hover:underline"
                                >
                                    + Añadir horario
                                </button>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700"
                                >
                                    Crear Zona
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
