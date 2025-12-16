<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    settings: Array,
});

// Helper to find setting value by key
const getSettingValue = (key) => {
    const setting = props.settings.find(s => s.key === key);
    return setting ? setting.value : '';
};

const form = useForm({
    settings: [
        { key: 'paypal_enabled', value: getSettingValue('paypal_enabled') === '1' || getSettingValue('paypal_enabled') === 'true' }, // Boolean
        { key: 'bank_transfer_enabled', value: getSettingValue('bank_transfer_enabled') === '1' || getSettingValue('bank_transfer_enabled') === 'true' }, // Boolean
        { key: 'bank_details_text', value: getSettingValue('bank_details_text') || '' }, // Text
    ]
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
             Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Configuración actualizada correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
};
</script>

<template>
    <Head title="Configuración" />

    <AdminLayout title="Configuración del Sistema">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800 p-8">
                <h3 class="text-lg font-bold text-secondary-900 dark:text-white mb-6 border-b border-primary-100 dark:border-secondary-800 pb-2">
                    Métodos de Pago
                </h3>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- PayPal Toggle -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-secondary-900 dark:text-white">Habilitar PayPal</label>
                            <p class="text-sm text-secondary-500">Permitir a los clientes pagar con PayPal.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.settings[0].value" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        </label>
                    </div>

                    <!-- Bank Transfer Toggle -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-secondary-900 dark:text-white">Habilitar Transferencia Bancaria</label>
                            <p class="text-sm text-secondary-500">Permitir pagos mediante depósito o transferencia.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                             <input type="checkbox" v-model="form.settings[1].value" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        </label>
                    </div>

                    <!-- Bank Details Textarea -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Datos Bancarios (Instrucciones)</label>
                        <textarea 
                            v-model="form.settings[2].value"
                            rows="4"
                            class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"
                            placeholder="Ingrese los datos de la cuenta bancaria aquí..."
                        ></textarea>
                         <p class="text-sm text-secondary-500 mt-1">Este texto aparecerá en los correos de confirmación.</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" 
                            :disabled="form.processing"
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium shadow-sm disabled:opacity-50"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
