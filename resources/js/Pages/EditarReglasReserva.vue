<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    reglas: Object,
});

const form = useForm({
    max_por_dia: props.reglas?.max_por_dia || "",
    max_por_semana: props.reglas?.max_por_semana || "",
    max_por_mes: props.reglas?.max_por_mes || "",
    max_por_anio: props.reglas?.max_por_anio || "",
});

const guardarCambios = () => {
    form.post("/reglas-reserva", {
        preserveScroll: true,
    });
};
</script>

<template>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configurar Reglas de Reserva
            </h2>
           
        </template>

        <div class="p-6 max-w-2xl mx-auto">

            <form @submit.prevent="guardarCambios" class="space-y-4">
                <div>
                    <label class="block font-semibold">Máximo por día</label>
                    <input type="number" v-model="form.max_por_dia" class="input" />
                </div>
                <div>
                    <label class="block font-semibold">Máximo por semana</label>
                    <input type="number" v-model="form.max_por_semana" class="input" />
                </div>
                <div>
                    <label class="block font-semibold">Máximo por mes</label>
                    <input type="number" v-model="form.max_por_mes" class="input" />
                </div>
                <div>
                    <label class="block font-semibold">Máximo por año</label>
                    <input type="number" v-model="form.max_por_anio" class="input" />
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Guardar Cambios
                </button>
            </form>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.375rem;
}
</style>
