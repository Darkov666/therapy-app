<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';

const props = defineProps({
    services: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('admin.services.index'), { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300));

const deleteService = (service) => {
    Swal.fire({
        title: '¿Eliminar servicio?',
        text: "Esta acción no se puede deshacer.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.services.destroy', service.id));
        }
    });
};

const formatCurrency = (amount, currency = 'MXN') => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};
</script>

<template>
    <Head title="Gestión de Servicios" />

    <AdminLayout title="Gestión de Servicios">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 text-secondary-900 dark:text-white">
            <h2 class="text-xl font-bold">Listado de Servicios</h2>
            
            <div class="flex space-x-4">
                <Link :href="route('admin.services.create')" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Servicio
                </Link>
                <input 
                    v-model="search" 
                    type="text" 
                    placeholder="Buscar..." 
                    class="rounded-lg border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                >
            </div>
        </div>

        <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                    <thead class="bg-primary-50 dark:bg-secondary-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Imagen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Vendedor</th> (<!-- NEW -->)
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-200 dark:divide-secondary-700 bg-white dark:bg-secondary-900">
                        <tr v-for="service in services.data" :key="service.id" class="hover:bg-primary-50 dark:hover:bg-secondary-800 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex-shrink-0 h-16 w-24">
                                    <img v-if="service.image_url" :src="service.image_url" class="h-16 w-24 object-cover rounded-md" alt="Service Image">
                                    <div v-else class="h-16 w-24 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-gray-400">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-secondary-900 dark:text-white">{{ service.title }}</div>
                                <div class="text-xs text-secondary-500">{{ service.duration_minutes }} min</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-secondary-500">{{ service.creator ? service.creator.name : 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ service.category ? service.category.name : 'Sin Categoría' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 mt-1 dark:text-secondary-400">
                                <div>{{ formatCurrency(service.price_mxn, 'MXN') }}</div>
                                <div class="text-xs text-gray-400">{{ formatCurrency(service.price_usd, 'USD') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 capitalize">
                                {{ service.type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link :href="route('admin.services.edit', service.id)" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </Link>
                                <button @click="deleteService(service)" class="text-red-600 hover:text-red-900 focus:outline-none" title="Eliminar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="services.data.length === 0">
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-secondary-500">
                                No se encontraron servicios.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-secondary-200 dark:border-secondary-700">
                <Pagination :links="services.links" />
            </div>
        </div>
    </AdminLayout>
</template>
