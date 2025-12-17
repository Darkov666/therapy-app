<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';
import Swal from 'sweetalert2';

const props = defineProps({
    sessions: Array,
});

const t = wTrans;
const password = ref('');
const confirmingLogout = ref(false);
const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    setTimeout(() => document.getElementById('password')?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('profile.sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
             Swal.fire({
                icon: 'success',
                title: 'Sesiones Cerradas',
                text: 'Se han cerrado todas las otras sesiones correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
        },
        onError: () => document.getElementById('password')?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
    <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-6">
        <header>
            <h2 class="text-lg font-medium text-secondary-900 dark:text-white">
                {{ t('profile.browser_sessions') || 'Sesiones de Navegador' }}
            </h2>

            <p class="mt-1 text-sm text-secondary-600 dark:text-secondary-400">
                {{ t('profile.browser_sessions_desc') || 'Administra y cierra tus sesiones activas en otros navegadores y dispositivos.' }}
            </p>
        </header>

        <!-- Sessions List -->
        <div class="mt-5 space-y-6" v-if="sessions.length > 0">
            <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                <div>
                   <svg v-if="session.agent.is_desktop" class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 17.25V15m-3 0-3-3m0 0-3 3m3-3V15" />
                    </svg>
                    <svg v-else class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>

                <div class="ml-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ session.agent.platform ? session.agent.platform : 'Unknown' }} - {{ session.agent.browser ? session.agent.browser : 'Unknown' }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ session.ip_address }},

                        <span v-if="session.is_current_device" class="text-green-500 font-semibold">This device</span>
                        <span v-else>Last active {{ session.last_active }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center mt-5">
            <button
                @click="confirmLogout"
                class="inline-flex items-center px-4 py-2 bg-secondary-800 dark:bg-secondary-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-secondary-800 uppercase tracking-widest hover:bg-secondary-700 dark:hover:bg-white focus:bg-secondary-700 dark:focus:bg-white active:bg-secondary-900 dark:active:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ t('profile.logout_other_browser_sessions') || 'Cerrar Otras Sesiones' }}
            </button>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        <div v-if="confirmingLogout" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-secondary-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">
                        {{ t('profile.logout_other_browser_sessions') || 'Cerrar Otras Sesiones' }}
                    </h3>

                    <div class="mt-4 text-sm text-secondary-600 dark:text-secondary-400">
                        {{ t('profile.logout_other_browser_sessions_help') || 'Por favor ingresa tu contraseña para confirmar que deseas cerrar sesión en tus otros dispositivos.' }}
                    </div>

                    <div class="mt-4">
                        <input
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4 border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Password"
                            @keyup.enter="logoutOtherBrowserSessions"
                        />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-2">{{ form.errors.password }}</div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="closeModal" class="ml-3 inline-flex items-center px-4 py-2 bg-white dark:bg-secondary-800 border border-gray-300 dark:border-secondary-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ t('Cancel') || 'Cancelar' }}
                        </button>

                        <button
                            class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="logoutOtherBrowserSessions"
                        >
                            {{ t('profile.logout_other_browser_sessions') || 'Cerrar Otras Sesiones' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
