<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head :title="`Usuario: ${user.name}`" />

    <AdminLayout title="Detalles del Usuario">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-bold text-secondary-900 dark:text-white">Perfil de Usuario</h2>
            <Link :href="route('admin.users.index')" class="text-primary-600 hover:text-primary-800">
                &larr; Volver a la lista
            </Link>
        </div>

        <div class="bg-white dark:bg-secondary-900 shadow overflow-hidden sm:rounded-lg border border-primary-100 dark:border-secondary-800">
            <div class="px-4 py-5 sm:px-6 flex items-center">
                 <img class="h-16 w-16 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-secondary-900 dark:text-white">
                        {{ user.name }}
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                            :class="{
                                'bg-purple-100 text-purple-800': user.role === 'admin',
                                'bg-blue-100 text-blue-800': user.role === 'psychologist',
                                'bg-green-100 text-green-800': user.role === 'patient',
                            }">
                            {{ user.role }}
                        </span>
                         <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="user.is_approved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ user.is_approved ? 'Activo' : 'Inactivo' }}
                        </span>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-secondary-500">
                        {{ user.email }} | @{{ user.nickname || 'Sin nickname' }}
                    </p>
                </div>
            </div>
            <div class="border-t border-secondary-200 dark:border-secondary-700 px-4 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-secondary-200 dark:divide-secondary-700">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Nombre Completo</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ user.name }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Email</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ user.email }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Teléfono</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ user.phone || 'No registrado' }}</dd>
                    </div> 
                    <div v-if="user.role === 'psychologist'" class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Especialidad</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ user.specialty || 'N/A' }}</dd>
                    </div>
                     <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Fecha de Registro</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ formatDate(user.created_at) }}</dd>
                    </div>
                     <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-secondary-500">Verificado (Email)</dt>
                        <dd class="mt-1 text-sm text-secondary-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ user.email_verified_at ? 'Sí' : 'No' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-3">
             <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring focus:ring-primary-300 disabled:opacity-25 transition">
                Editar Usuario
            </Link>
        </div>

    </AdminLayout>
</template>
