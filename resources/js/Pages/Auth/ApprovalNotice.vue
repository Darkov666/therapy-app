<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';

const t = wTrans;
</script>

<template>
    <MainLayout>
        <Head title="Pending Approval" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-4">
                    {{ t('auth.account_pending') || 'Cuenta Pendiente de Aprobación' }}
                </h2>

                <p class="text-sm text-secondary-600 dark:text-secondary-400 mb-6">
                    {{ t('auth.account_pending_desc') || 'Tu cuenta de Psicólogo ha sido creada exitosamente, pero requiere aprobación de un administrador antes de poder acceder. Te notificaremos por correo electrónico una vez que sea activada.' }}
                </p>

                <div class="flex justify-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline text-sm text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ t('auth.logout') || 'Cerrar Sesión' }}
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
