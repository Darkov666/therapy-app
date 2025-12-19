<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
             // The backend UpdateUserPassword action should have cleared the flag,
             // so the modal will close automatically as the page prop updates.
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
    <Modal :show="showModal" :closeable="false">
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
                    <InputLabel for="current_password" value="Contraseña Actual" />
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="current-password"
                    />
                    <InputError :message="form.errors.current_password" class="mt-2" />
                </div>

                <!-- New Password -->
                <div class="mt-4 col-span-6 sm:col-span-4">
                    <InputLabel for="password" value="Nueva Contraseña" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4 col-span-6 sm:col-span-4">
                    <InputLabel for="password_confirmation" value="Confirmar Nueva Contraseña" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="updatePassword"
                >
                    Guardar Nueva Contraseña
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
