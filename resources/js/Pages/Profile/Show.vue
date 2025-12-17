<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    sessions: Array,
});

const page = usePage();
// Dynamically choose layout based on role? Or just use MainLayout logic?
// Ideally, admin should stay in AdminLayout.
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value.role === 'admin');
</script>

<template>
    <Head title="Profile" />

    <component :is="isAdmin ? AdminLayout : MainLayout">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Perfil de Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Profile Information (Placeholder for future) -->
                <div class="p-4 sm:p-8 bg-white dark:bg-secondary-800 shadow sm:rounded-lg">
                    <section>
                         <h2 class="text-lg font-medium text-secondary-900 dark:text-white">Información del Perfil</h2>
                         <p class="mt-1 text-sm text-secondary-600 dark:text-secondary-400">
                            Próximamente podrás editar tu información aquí.
                         </p>
                    </section>
                </div>

                <!-- Sessions Management -->
                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />
            </div>
        </div>
    </component>
</template>
