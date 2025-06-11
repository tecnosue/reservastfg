<script setup>
import { Head, router } from "@inertiajs/vue3";

// const logout = () => {
//     router.post(
//         route("logout"),
//         {},
//         {
//             onFinish: () => {
//                 router.visit("/"); // redireccionar a la página de inicio después de cerrar sesión
//             }
//         }
//     );
// };
// const logout = () => {
//     router.post(route("logout"), {}, {
//         onFinish: () => {
//             // Forzar recarga completa de la página
//             window.location.replace('/');
//         }
//     });
// };
const logout = async () => {
    try {
        // Hacer logout con fetch nativo para evitar Inertia
        await fetch(route('logout'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        });
        
        // Limpiar todo el almacenamiento
        localStorage.clear();
        sessionStorage.clear();
        
        // Forzar navegación completa (no SPA)
        window.location.href = '/';
        
    } catch (error) {
        console.error('Error durante logout:', error);
        // En caso de error, forzar redirección de todas formas
        window.location.href = '/';
    }
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
