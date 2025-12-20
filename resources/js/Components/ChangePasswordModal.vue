<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();

// Compute if the modal should be open
const showModal = computed(() => {
    return page.props.auth.user && page.props.auth.user.must_change_password;
});

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => {
             form.reset();
             Swal.fire({
                icon: 'success',
                title: 'Contraseña Actualizada',
                text: 'Tu contraseña ha sido actualizada correctamente.',
                timer: 2000,
                showConfirmButton: false
             });
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }
            if (form.errors.current_password) {
                form.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white dark:bg-secondary-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="p-6 bg-white dark:bg-secondary-800">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Seguridad de la Cuenta
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Por seguridad, es necesario que cambies tu contraseña antes de continuar.
                    </p>

                    <div class="mt-6">
                        <!-- Current Password -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="current_password">
                                Contraseña Actual
                            </label>
                            <input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                                autocomplete="current-password"
                            />
                            <div v-if="form.errors.current_password" class="text-red-600 text-sm mt-2">
                                {{ form.errors.current_password }}
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mt-4 col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password">
                                Nueva Contraseña
                            </label>
                            <input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4 col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password_confirmation">
                                Confirmar Nueva Contraseña
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-2">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="updatePassword"
                        >
                            Guardar Nueva Contraseña
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
