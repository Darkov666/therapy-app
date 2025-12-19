<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    title: String,
});

const t = wTrans;
const showingSidebar = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const menuItems = [
    { name: 'Dashboard', route: 'admin.dashboard', active: 'admin.dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' },
    { name: 'Usuarios', route: 'admin.users.index', active: 'admin.users.*', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Servicios', route: 'admin.services.index', active: 'admin.services.*', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { name: 'Productos', route: 'admin.products.index', active: 'admin.products.*', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'Categorías', route: 'admin.categories.index', active: 'admin.categories.*', icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z' },
    { name: 'Mensajes', route: 'admin.messages.index', active: 'admin.messages.*', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { name: 'Blog', route: 'admin.blog.index', active: 'admin.blog.*', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z' },
    { name: 'Configuración', route: 'admin.settings', active: 'admin.settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];

import ChangePasswordModal from '@/Components/ChangePasswordModal.vue';

</script>

<template>
    <div class="min-h-screen bg-primary-50 dark:bg-secondary-950 flex transition-colors duration-300 font-sans">
        <!-- Sidebar -->
        <aside :class="{'translate-x-0': showingSidebar, '-translate-x-full': !showingSidebar}" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-secondary-900 border-r border-primary-100 dark:border-secondary-800 transition-transform duration-300 md:relative md:translate-x-0">
            <div class="h-20 flex items-center justify-center border-b border-primary-100 dark:border-secondary-800">
                <Link href="/" class="text-2xl font-serif font-bold text-primary-600 dark:text-primary-400">
                    Admin Panel
                </Link>
            </div>
            
            <nav class="mt-6 px-4 space-y-2">
                <Link v-for="item in menuItems" :key="item.name" :href="route(item.route)" 
                    :class="{'bg-primary-50 text-primary-600 dark:bg-secondary-800 dark:text-primary-400': route().current(item.active), 'text-secondary-600 hover:bg-primary-50 hover:text-primary-600 dark:text-secondary-400 dark:hover:bg-secondary-800 dark:hover:text-white': !route().current(item.active)}"
                    class="flex items-center px-4 py-3 rounded-xl transition duration-200 group">
                    <svg class="h-6 w-6 mr-3 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span class="font-medium">{{ item.name }}</span>
                </Link>
            </nav>

            <div class="absolute bottom-0 w-full p-4 border-t border-primary-100 dark:border-secondary-800">
                 <button @click="logout" class="flex items-center w-full px-4 py-3 text-secondary-600 dark:text-secondary-400 hover:text-red-600 dark:hover:text-red-400 transition">
                    <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium">Cerrar Sesión</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white dark:bg-secondary-900 border-b border-primary-100 dark:border-secondary-800 h-20 flex items-center justify-between px-6">
                <button @click="showingSidebar = !showingSidebar" class="md:hidden text-secondary-500 hover:text-primary-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-secondary-800 dark:text-white">{{ title }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                     <!-- User dropdown could go here -->
                     <div class="text-sm text-secondary-600 dark:text-secondary-400">
                        {{ $page.props.auth.user.name }}
                     </div>
                     <img :src="$page.props.auth.user.profile_photo_url" class="h-10 w-10 rounded-full border border-primary-200 object-cover" alt="Admin Profile">
                </div>
            </header>

            <!-- Actions Toolbar (Optional) -->
            <div v-if="$slots.actions" class="px-6 py-4 bg-white dark:bg-secondary-900 border-b border-primary-100 dark:border-secondary-800 flex justify-end">
                <slot name="actions" />
            </div>

            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-primary-50 dark:bg-secondary-950 p-6">
                <slot />
            </main>
        </div>
        <ChangePasswordModal />
    </div>
</template>
