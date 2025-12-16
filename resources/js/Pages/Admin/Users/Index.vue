<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { wTrans } from 'laravel-vue-i18n';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    users: Array,
});

const t = wTrans;
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AdminLayout title="Usuarios y Personal">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Lista de Personal</h2>
             <!-- Future: Create Button -->
            <!-- <Link :href="route('admin.users.create')" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm">
                Crear Nuevo
            </Link> -->
        </div>

        <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800">
            <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                <thead class="bg-primary-50 dark:bg-secondary-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-200 dark:divide-secondary-700">
                    <tr v-for="user in users" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-secondary-900 dark:text-white">{{ user.name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-secondary-400">
                            {{ user.email }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': user.role === 'admin',
                                    'bg-blue-100 text-blue-800': user.role === 'titular',
                                    'bg-gray-100 text-gray-800': user.role === 'client'
                                }">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3">Editar</button>
                        </td>
                    </tr>
                     <tr v-if="users.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-secondary-500">
                            No se encontraron usuarios administrativos.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
