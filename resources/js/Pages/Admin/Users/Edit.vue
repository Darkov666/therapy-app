<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
    nickname: props.user.nickname,
    email: props.user.email,
    phone: props.user.phone,
    role: props.user.role,
    specialty: props.user.specialty,
    is_approved: Boolean(props.user.is_approved),
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head :title="`Editar: ${user.name}`" />

    <AdminLayout title="Editar Usuario">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-bold text-secondary-900 dark:text-white">Editar Usuario</h2>
            <Link :href="route('admin.users.index')" class="text-primary-600 hover:text-primary-800">
                Cancel
            </Link>
        </div>

        <div class="max-w-3xl mx-auto bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800 p-6">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Nombre Completo</label>
                        <input v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <!-- Nickname -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Nickname (Usuario)</label>
                        <input v-model="form.nickname" type="text" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                        <div v-if="form.errors.nickname" class="text-red-500 text-xs mt-1">{{ form.errors.nickname }}</div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Correo Electrónico</label>
                        <input v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required />
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Teléfono</label>
                        <input v-model="form.phone" type="text" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                         <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Rol</label>
                        <select v-model="form.role" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="patient">Paciente</option>
                            <option value="psychologist">Psicólogo</option>
                            <option value="admin">Administrador</option>
                        </select>
                         <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                    </div>

                    <!-- Specialty (Only if Psychologist) -->
                    <div v-if="form.role === 'psychologist'">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">Especialidad</label>
                        <input v-model="form.specialty" type="text" class="mt-1 block w-full border-gray-300 dark:border-secondary-700 dark:bg-secondary-800 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                        <div v-if="form.errors.specialty" class="text-red-500 text-xs mt-1">{{ form.errors.specialty }}</div>
                    </div>
                </div>

                <!-- Approval Status -->
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="form.is_approved" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-secondary-600 dark:text-secondary-400">Usuario Aprobado / Activo</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Si se desmarca, el usuario no podrá iniciar sesión.</p>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button class="ml-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" :disabled="form.processing">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
