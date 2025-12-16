<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ClientFormFields from '@/Components/ClientFormFields.vue';
import { wTrans } from 'laravel-vue-i18n';
import Swal from 'sweetalert2';

const props = defineProps({
    service: Object,
    psychologists: Array,
});

const t = wTrans;
const form = useForm({
    name: '',
    surname: '',
    email: '',
    phone: '',
    gender: '',
    custom_gender: '',
    photo: null,
    session_type: 'online',
    service_id: props.service?.id || null,
    psychologist_id: null,
});

const submit = () => {
    if (form.gender === 'other' && form.custom_gender) {
        form.gender = form.custom_gender;
    }

    // Gender validation
    if (!form.gender) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.missing_gender') || 'Género requerido',
            text: t('booking.select_gender_desc') || 'Por favor selecciona un género.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    // Phone validation (10 digits)
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(form.phone)) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.invalid_phone') || 'Teléfono inválido',
            text: t('booking.invalid_phone_desc') || 'El teléfono debe tener 10 dígitos numéricos.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.invalid_email') || 'Correo inválido',
            text: t('booking.invalid_email_desc') || 'Por favor ingresa un correo electrónico válido.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    if (!form.photo) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.photo_required') || 'Foto requerida',
            text: t('booking.photo_required_desc') || 'Por favor toma o sube una foto personal para continuar.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }
    form.post('/booking/individual', {
        onSuccess: () => {
            // Redirect handled by backend (to payment or calendar)
        }
    });
};
</script>

<template>
    <Head :title="t('booking.individual_title')" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            {{ t('booking.individual_registration') }}
                        </h2>
                        <form @submit.prevent="submit" class="space-y-6">
                            <ClientFormFields :form="form" :psychologists="psychologists" />

                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-50">
                                    {{ t('booking.continue_to_calendar') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
