<script setup>
import { computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    status: String,
});

const t = wTrans;
const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <MainLayout>
        <Head title="Email Verification" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-4 text-sm text-secondary-600 dark:text-secondary-400">
                    {{ t('auth.verify_email_intro') || 'Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.' }}
                </div>

                <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ t('auth.verification_link_sent') || 'Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.' }}
                </div>

                <form @submit.prevent="submit">
                    <div class="mt-4 flex items-center justify-between">
                        <button
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('auth.resend_verification_email') || 'Reenviar Email de Verificación' }}
                        </button>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="underline text-sm text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ t('auth.logout') || 'Cerrar Sesión' }}
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
