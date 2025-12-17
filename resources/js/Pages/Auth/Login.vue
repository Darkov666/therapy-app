<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    status: {
        type: String,
    },
});

const t = wTrans;

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <MainLayout>
        <Head title="Log in" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-secondary-900 dark:text-white">
                        {{ t('auth.login_title') || 'Iniciar Sesión' }}
                    </h2>
                    <p class="text-sm text-secondary-500 dark:text-secondary-400 mt-2">
                        {{ t('auth.login_desc') || 'Accede a tu cuenta operativa' }}
                    </p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="email">
                            {{ t('auth.email_or_nickname') || 'Correo Electrónico o Usuario' }}
                        </label>
                        <input
                            id="email"
                            type="text"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-2">{{ form.errors.email }}</div>
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="password">
                            {{ t('auth.password') || 'Contraseña' }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="remember"
                                v-model="form.remember"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm text-secondary-600 dark:text-secondary-400">{{ t('auth.remember_me') || 'Recordarme' }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button
                            class="ml-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('auth.login_button') || 'Ingresar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
