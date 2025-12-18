<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { wTrans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const t = wTrans;
const page = usePage();
const currentUserRole = computed(() => page.props.auth.user.role);

const props = defineProps({
    allowed_roles: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    name: '',
    nickname: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.allowed_roles.length > 0 ? props.allowed_roles[0].value : 'patient', // Default to first available
    phone: '',
    specialty: '',
    gender: '',
});

const availableRoles = computed(() => props.allowed_roles);

const submit = () => {
    form.post(route('admin.users.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AdminLayout title="Crear Usuario">
        <Head title="Crear Usuario" />

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Información del Usuario</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Crea un nuevo usuario en el sistema. Se enviará un correo de verificación.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form @submit.prevent="submit">
                        <div class="px-4 py-5 bg-white dark:bg-secondary-800 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="grid grid-cols-6 gap-6">
                                <!-- Name -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="name">
                                        Nombre Completo
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                        autofocus
                                    />
                                    <div v-if="form.errors.name" class="text-red-600 text-sm mt-2">{{ form.errors.name }}</div>
                                </div>

                                <!-- Nickname -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="nickname">
                                        Nickname (Opcional)
                                    </label>
                                    <input
                                        id="nickname"
                                        v-model="form.nickname"
                                        type="text"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    />
                                    <div v-if="form.errors.nickname" class="text-red-600 text-sm mt-2">{{ form.errors.nickname }}</div>
                                </div>

                                <!-- Email -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="email">
                                        Correo Electrónico
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="text-red-600 text-sm mt-2">{{ form.errors.email }}</div>
                                </div>

                                <!-- Role -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="role">
                                        Rol
                                    </label>
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                    >
                                        <option v-for="role in availableRoles" :key="role.value" :value="role.value">
                                            {{ role.label }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.role" class="text-red-600 text-sm mt-2">{{ form.errors.role }}</div>
                                </div>
                                
                                <!-- Gender -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="gender">
                                        Género
                                    </label>
                                    <select
                                            id="gender"
                                            v-model="form.gender"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        >
                                        <option value="">Seleccionar</option>
                                        <option value="male">Masculino</option>
                                        <option value="female">Femenino</option>
                                        <option value="other">Otro</option>
                                    </select>
                                    <div v-if="form.errors.gender" class="text-red-600 text-sm mt-2">{{ form.errors.gender }}</div>
                                </div>

                                <!-- Phone -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="phone">
                                        Teléfono (Opcional)
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    />
                                    <div v-if="form.errors.phone" class="text-red-600 text-sm mt-2">{{ form.errors.phone }}</div>
                                </div>

                                <!-- Specialty (Psychologist Only) -->
                                <div v-if="form.role === 'psychologist' || form.role === 'titular'" class="col-span-6">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="specialty">
                                        Especialidad
                                    </label>
                                    <input
                                        id="specialty"
                                        v-model="form.specialty"
                                        type="text"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    />
                                    <div v-if="form.errors.specialty" class="text-red-600 text-sm mt-2">{{ form.errors.specialty }}</div>
                                </div>

                                <!-- Password -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password">
                                        Contraseña
                                    </label>
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                                </div>

                                <!-- Password Confirmation -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password_confirmation">
                                        Confirmar Contraseña
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                        autocomplete="new-password"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-secondary-700 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                             <button
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
