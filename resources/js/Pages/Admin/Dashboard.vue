<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { wTrans } from 'laravel-vue-i18n';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    metrics: Object,
});

const t = wTrans;

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout title="Panel de Control">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Ventas Totales (Citas)</p>
                        <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ metrics.total_sales }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Citas Pendientes</p>
                        <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ metrics.pending_appointments }}</p>
                    </div>
                </div>
            </div>

            <!-- Confirmed Appointments -->
            <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Citas Confirmadas</p>
                        <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ metrics.confirmed_appointments }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-500 dark:text-secondary-400">Usuarios Registrados</p>
                        <p class="text-2xl font-bold text-secondary-900 dark:text-white">{{ metrics.total_users }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Psychologists -->
             <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800">
                <h3 class="text-lg font-bold text-secondary-900 dark:text-white mb-4">Top Psicólogos (Citas Confirmadas)</h3>
                <div v-if="metrics.top_psychologists && metrics.top_psychologists.length > 0" class="overflow-x-auto">
                     <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Psicólogo</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Total Citas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100 dark:divide-secondary-800">
                            <tr v-for="item in metrics.top_psychologists" :key="item.psychologist_id">
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-secondary-700 dark:text-secondary-300">
                                    {{ item.psychologist ? item.psychologist.name : 'Desconocido' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-right font-bold text-primary-600">
                                    {{ item.total }}
                                </td>
                            </tr>
                        </tbody>
                     </table>
                </div>
                <div v-else class="text-secondary-500 text-sm py-4 text-center">
                    No hay datos suficientes aún.
                </div>
             </div>

             <!-- Placeholder for Chart or Recent Activity -->
             <div class="bg-white dark:bg-secondary-900 overflow-hidden shadow-sm rounded-xl p-6 border border-primary-100 dark:border-secondary-800 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-secondary-400 mb-2">Gráfica de Ventas (Próximamente)</p>
                    <div class="h-40 w-full bg-primary-50 dark:bg-secondary-800 rounded-lg animate-pulse flex items-center justify-center">
                        <span class="text-xs text-secondary-300">Chart Placeholder</span>
                    </div>
                </div>
             </div>
        </div>
    </AdminLayout>
</template>
