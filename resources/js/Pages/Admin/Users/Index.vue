<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { wTrans } from 'laravel-vue-i18n';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';

const props = defineProps({
    users: Object,
    filters: Object,
    can_create: Boolean, 
});

const t = wTrans;
const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');

watch(search, debounce((value) => {
    router.get(route('admin.users.index'), { search: value, role: role.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300));

watch(role, (value) => {
    router.get(route('admin.users.index'), { search: search.value, role: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const toggleStatus = (user) => {
    const action = user.is_approved ? 'Desactivar' : 'Activar';
    Swal.fire({
        title: `¿${action} usuario?`,
        text: `El usuario será ${action.toLowerCase()} y ${user.is_approved ? 'perderá' : 'obtendrá'} acceso.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Sí, ${action.toLowerCase()}`
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('admin.users.approve', user.id));
        }
    });
};

const deleteUser = (user) => {
     Swal.fire({
        title: '¿Eliminar usuario?',
        text: "Esta acción no se puede deshacer.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.users.destroy', user.id));
        }
    });
}
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AdminLayout title="Gestión de Usuarios">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 text-secondary-900 dark:text-white">
            <h2 class="text-xl font-bold">Listado de Usuarios</h2>
            
            <div class="flex space-x-4">
                <Link :href="route('admin.users.create')" v-if="can_create" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Usuario
                </Link>
                <input 
                    v-model="search" 
                    type="text" 
                    placeholder="Buscar..." 
                    class="rounded-lg border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                >
                <select 
                    v-model="role" 
                    class="rounded-lg border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                >
                    <option value="">Todos los Roles</option>
                    <option value="patient">Pacientes</option>
                    <option value="psychologist">Psicólogos</option>
                    <option value="admin">Administradores</option>
                </select>
            </div>
        </div>

        <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                    <thead class="bg-primary-50 dark:bg-secondary-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Registrado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-200 dark:divide-secondary-700 bg-white dark:bg-secondary-900">
                        <tr v-for="user in users.data" :key="user.id" 
                            @click="router.get(route('admin.users.show', user.id))"
                            class="hover:bg-primary-50 dark:hover:bg-secondary-800 cursor-pointer transition"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-secondary-900 dark:text-white">
                                            {{ user.name }}
                                            <div class="text-xs text-secondary-500" v-if="user.nickname">@{{ user.nickname }}</div>
                                        </div>
                                        <div class="text-sm text-secondary-500 dark:text-secondary-400">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    :class="{
                                        'bg-purple-100 text-purple-800': user.role === 'admin',
                                        'bg-blue-100 text-blue-800': user.role === 'psychologist',
                                        'bg-green-100 text-green-800': user.role === 'patient',
                                    }">
                                    {{ user.role === 'patient' ? 'Paciente' : (user.role === 'psychologist' ? 'Psicólogo' : 'Admin') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="user.is_approved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ user.is_approved ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-secondary-400">
                                {{ user.created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2" @click.stop>
                                <Link :href="route('admin.users.show', user.id)" class="text-blue-600 hover:text-blue-900" title="Ver Detalles">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </Link>
                                <Link :href="route('admin.users.edit', user.id)" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </Link>
                                <button @click="toggleStatus(user)" 
                                    class="text-gray-600 hover:text-gray-900 focus:outline-none" 
                                    :title="user.is_approved ? 'Desactivar Usuario' : 'Activar Usuario'"
                                >
                                    <svg v-if="user.is_approved" class="w-5 h-5 inline text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    <svg v-else class="w-5 h-5 inline text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </button>
                                <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900 focus:outline-none" title="Eliminar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-secondary-500">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-secondary-200 dark:border-secondary-700">
                <Pagination :links="users.links" />
            </div>
        </div>
    </AdminLayout>
</template>
