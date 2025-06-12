<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    reglas: Object,
    grupos: Array,
});
// Si ya existe una regla para un grupo, la usamos; si no, tomamos el primer grupo
const defaultGrupo = props.reglas?.grupo_id
  ?? (props.grupos.length > 0 ? props.grupos[0].id : "");

const form = useForm({
  grupo_id      : defaultGrupo,
  max_por_dia   : props.reglas?.max_por_dia   || "",
  max_por_semana: props.reglas?.max_por_semana|| "",
  max_por_mes   : props.reglas?.max_por_mes   || "",
  max_por_anio  : props.reglas?.max_por_anio  || "",
});
console.log("Formulario a enviar:", form);

const guardarCambios = () => {
    form.put(route("reglas.update"), {
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
                    <label class="block font-semibold">Grupo</label>
                    <select v-model="form.grupo_id" class="input">
                        <option disabled value="">Selecciona un grupo</option>

                        <option
                            v-for="grupo in props.grupos"
                            :key="grupo.id"
                            :value="grupo.id"
                        >
                            {{ grupo.nombre }}
                        </option>
                    </select>
                

                    <div>
                        <label class="block font-semibold">Máximo por día</label>
                        <input
                            type="number"
                            v-model="form.max_por_dia"
                            class="input"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold">Máximo por semana</label>
                        <input
                            type="number"
                            v-model="form.max_por_semana"
                            class="input"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold">Máximo por mes</label>
                        <input
                            type="number"
                            v-model="form.max_por_mes"
                            class="input"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold">Máximo por año</label>
                        <input
                            type="number"
                            v-model="form.max_por_anio"
                            class="input"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
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
