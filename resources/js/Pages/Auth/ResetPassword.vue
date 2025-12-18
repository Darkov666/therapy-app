<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    email: String,
    token: String,
});

const t = wTrans;

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <MainLayout>
        <Head title="Reset Password" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg">
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

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="password">
                            {{ t('auth.password') || 'Contraseña' }}
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="password_confirmation">
                            {{ t('auth.confirm_password') || 'Confirmar Contraseña' }}
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-2">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('auth.reset_password') || 'Restablecer Contraseña' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
