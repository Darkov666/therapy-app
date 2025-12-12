<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';

const props = defineProps({
    items: Array, // Items that require scheduling
});

const t = wTrans;

// Initialize form with services needing dates
// We will store: { service_id: { date: 'YYYY-MM-DD', time: 'HH:MM', notes: '' } }
const form = useForm({
    appointments: props.items.reduce((acc, item) => {
        acc[item.id] = {
            cart_item_id: item.id,
            service_title: item.service.title,
            date: '',
            time: '',
            notes: '',
        };
        return acc;
    }, {})
});

const availableTimes = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];

const submit = () => {
    form.post(route('scheduling.store'), {
        onSuccess: () => {
            // Redirect to checkout handled by backend
        }
    });
};

// Helper to check if all fields are filled
const isFormValid = computed(() => {
    return Object.values(form.appointments).every(a => a.date && a.time);
});
</script>

<template>
    <Head title="Agendar Citas" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-secondary-900 dark:text-white mb-8">Agendar Sesiones</h1>

                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg p-6">
                    <p class="mb-6 text-secondary-600 dark:text-secondary-400">
                        Por favor selecciona fecha y hora para tus servicios de terapia antes de proceder al pago.
                    </p>

                    <form @submit.prevent="submit" class="space-y-8">
                        <div v-for="item in items" :key="item.id" class="border-b border-gray-200 dark:border-secondary-700 pb-8 last:border-0 last:pb-0">
                            <h3 class="text-xl font-semibold text-primary-700 dark:text-primary-300 mb-4">
                                {{ item.service.title }}
                                <span class="text-sm font-normal text-secondary-500">({{ item.service.duration_minutes }} min)</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Fecha</label>
                                    <input type="date" v-model="form.appointments[item.id].date" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 focus:ring-primary-500 focus:border-primary-500" required :min="new Date().toISOString().split('T')[0]" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Hora</label>
                                    <select v-model="form.appointments[item.id].time" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 focus:ring-primary-500 focus:border-primary-500" required>
                                        <option value="" disabled>Seleccionar hora</option>
                                        <option v-for="time in availableTimes" :key="time" :value="time">{{ time }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit" :disabled="form.processing || !isFormValid" class="px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                Continuar al Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
