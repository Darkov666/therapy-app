<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';

defineProps({
    status: String,
});

const t = wTrans;

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <MainLayout>
        <Head title="Forgot Password" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-4 text-sm text-secondary-600 dark:text-secondary-400">
                    {{ t('auth.forgot_password_desc') || '¿Olvidaste tu contraseña? No hay problema. Simplemente haznos saber tu dirección de correo electrónico y te enviaremos un enlace para restablecer la contraseña que te permitirá elegir una nueva.' }}
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="email">
                            {{ t('auth.email') || 'Correo Electrónico' }}
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="username"
                        />
                         <div v-if="form.errors.email" class="text-red-600 text-sm mt-2">{{ form.errors.email }}</div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                         <Link :href="route('login')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mx-4">
                            {{ t('auth.cancel') || 'Cancelar' }}
                        </Link>
                        <button
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('auth.email_password_reset_link') || 'Enviar Enlace de Recuperación' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
