<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';
import { ref } from 'vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const t = wTrans;
const photoPreview = ref(null);
const photoInput = ref(null);

const form = useForm({
    name: '',
    nickname: '',
    email: '',
    phone: '',
    role: 'patient',
    specialty: '',
    password: '',
    password_confirmation: '',
    terms: false,
    photo: null,
    fax: '', // Honeypot
});

const submit = () => {
    if (form.fax) return; // Client-side honeypot check

    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
    form.photo = photo;
};
</script>

<template>
    <MainLayout>
        <Head title="Register" />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-secondary-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-secondary-800 shadow-md overflow-hidden sm:rounded-lg">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-secondary-900 dark:text-white">
                        {{ t('auth.register_title') || 'Crear Cuenta' }}
                    </h2>
                    <p class="text-sm text-secondary-500 dark:text-secondary-400 mt-2">
                        {{ t('auth.register_desc') || 'Únete a nuestra comunidad' }}
                    </p>
                </div>

                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <!-- Moneypot (Hidden) -->
                    <input type="text" v-model="form.fax" class="hidden" autocomplete="off">

                    <!-- Profile Photo -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300">
                            {{ t('auth.profile_photo') || 'Foto de Perfil' }}
                        </label>
                        <div class="mt-2 flex items-center">
                            <div v-if="photoPreview" class="mr-3">
                                <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                                      :style="'background-image: url(\'' + photoPreview + '\');'" />
                            </div>
                            <div v-else class="mr-3">
                                <span class="inline-block h-20 w-20 rounded-full overflow-hidden bg-gray-100">
                                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            </div>
                            <input
                                ref="photoInput"
                                type="file"
                                class="hidden"
                                @change="updatePhotoPreview"
                            >
                            <button
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-secondary-700 border border-secondary-300 dark:border-secondary-600 rounded-md font-semibold text-xs text-secondary-700 dark:text-secondary-300 uppercase tracking-widest shadow-sm hover:text-secondary-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-secondary-800 active:bg-secondary-50 transition ease-in-out duration-150"
                                @click.prevent="photoInput.click()"
                            >
                                {{ t('auth.select_photo') || 'Seleccionar Foto' }}
                            </button>
                        </div>
                        <div v-if="form.errors.photo" class="text-red-600 text-sm mt-2">{{ form.errors.photo }}</div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="name">
                            {{ t('auth.name') || 'Nombre Completo' }}
                        </label>
                        <input
                            id="name"
                            type="text"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-2">{{ form.errors.name }}</div>
                    </div>

                    <!-- Nickname -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="nickname">
                            {{ t('auth.nickname') || 'Nickname (Usuario)' }}
                        </label>
                        <input
                            id="nickname"
                            type="text"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.nickname"
                            required
                            autocomplete="username"
                        />
                        <div v-if="form.errors.nickname" class="text-red-600 text-sm mt-2">{{ form.errors.nickname }}</div>
                    </div>

                    <!-- Email -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="email">
                            {{ t('auth.email') || 'Correo Electrónico' }}
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.email"
                            required
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-2">{{ form.errors.email }}</div>
                    </div>

                    <!-- Phone -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="phone">
                            {{ t('auth.phone') || 'Teléfono (para 2FA)' }}
                        </label>
                        <input
                            id="phone"
                            type="text"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.phone"
                            required
                        />
                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-2">{{ form.errors.phone }}</div>
                    </div>

                    <!-- Role -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="role">
                            {{ t('auth.account_type') || 'Tipo de Cuenta' }}
                        </label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                        >
                            <option value="patient">{{ t('role.patient') || 'Paciente / Visitante' }}</option>
                            <option value="psychologist">{{ t('role.psychologist') || 'Psicólogo' }}</option>
                        </select>
                        <div v-if="form.errors.role" class="text-red-600 text-sm mt-2">{{ form.errors.role }}</div>
                    </div>

                    <!-- Specialty (Conditional) -->
                    <div v-if="form.role === 'psychologist'" class="mt-4">
                         <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="specialty">
                            {{ t('auth.specialty') || 'Especialidad' }}
                        </label>
                        <input
                            id="specialty"
                            type="text"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.specialty"
                            placeholder="Ej. Terapia Cognitivo Conductual"
                        />
                         <div v-if="form.errors.specialty" class="text-red-600 text-sm mt-2">{{ form.errors.specialty }}</div>
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="password">
                            {{ t('auth.password') || 'Contraseña' }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-secondary-700 dark:text-secondary-300" for="password_confirmation">
                            {{ t('auth.confirm_password') || 'Confirmar Contraseña' }}
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            class="border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-2">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <!-- Terms -->
                     <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" v-model="form.terms" required class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
                            <span class="ml-2 text-sm text-secondary-600 dark:text-secondary-400">
                                {{ t('auth.agree_terms') || 'Acepto los Términos y Condiciones' }}
                            </span>
                        </label>
                        <div v-if="form.errors.terms" class="text-red-600 text-sm mt-2">{{ form.errors.terms }}</div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <Link
                            href="/login"
                            class="underline text-xs text-secondary-600 dark:text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            {{ t('auth.already_registered') || '¿Ya tienes cuenta?' }}
                        </Link>

                        <button
                            class="ml-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('auth.register_button') || 'Registrarse' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
