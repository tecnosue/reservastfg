<script setup>
import { Head, router } from "@inertiajs/vue3";

const logout = () => {
    router.post(
        route("logout"),
        {},
        {
            onFinish: () => {
                router.visit("/"); // redireccionar a la página de inicio después de cerrar sesión
            }
        }
    );
};
</script>

<template>
    <Head title="Cuenta pendiente" />

    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="text-center p-6 max-w-md bg-white rounded shadow">
            <h2 class="text-xl font-bold mb-4">
                Cuenta pendiente de activación
            </h2>
            <p class="text-gray-600">
                Tu cuenta está pendiente de activación por parte del
                administrador.
            </p>

            <button
                class="mt-6 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
                @click="logout"
            >
                Aceptar
            </button>
        </div>
    </div>
</template>
