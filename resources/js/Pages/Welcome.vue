<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import GallerySection from '@/Components/GallerySection.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import { wTrans } from 'laravel-vue-i18n';

defineProps({
    featuredServices: Array,
    latestPosts: Array,
});

const isBookingModalOpen = ref(false);

const openBookingModal = () => {
    isBookingModalOpen.value = true;
};

const closeBookingModal = () => {
    isBookingModalOpen.value = false;
};

const galleryCategories = [
    {
        title: 'gallery.individual',
        images: [
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1544027993-37dbfe43562a?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1527613426441-4da17471b66d?q=80&w=800&auto=format&fit=crop',
        ]
    },
    {
        title: 'gallery.group',
        images: [
            'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=800&auto=format&fit=crop',
        ]
    },
    {
        title: 'gallery.workshops',
        images: [
            'https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=800&auto=format&fit=crop',
        ]
    },
    {
        title: 'gallery.conferences',
        images: [
            'https://images.unsplash.com/photo-1475721027767-4d06cdd392ef?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1544531696-b8536437d577?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=800&auto=format&fit=crop',
        ]
    }
];
</script>

<template>
    <Head title="Welcome" />

    <MainLayout>
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-primary-50 to-primary-100 dark:from-secondary-900 dark:to-secondary-800 overflow-hidden min-h-[calc(100vh-5rem)] flex items-center justify-center pb-20">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center py-12 lg:py-0">
                <h1 class="text-4xl tracking-tight font-serif font-extrabold text-secondary-900 dark:text-white sm:text-5xl md:text-6xl max-w-3xl">
                    <span class="block whitespace-pre-line">{{ $t('hero.title') }}</span> 
                </h1>
                <p class="mt-6 text-lg text-secondary-500 dark:text-secondary-300 max-w-2xl mx-auto">
                    {{ $t('hero.subtitle') }}
                </p>
                
                <!-- Psychologist Photo (Centered) -->
                <div class="mt-10 relative w-48 h-48 sm:w-64 sm:h-64 lg:w-80 lg:h-80">
                    <div class="absolute inset-0 bg-secondary-200 dark:bg-secondary-700 rounded-full shadow-2xl flex items-center justify-center overflow-hidden border-4 border-white dark:border-secondary-600">
                         <img src="/images/Ejecutiva.png" alt="Psychologist Photo" class="w-full h-full object-cover">
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-primary-200 dark:bg-primary-900 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-secondary-300 dark:bg-secondary-600 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                </div>

                <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 w-full sm:w-auto">
                    <Link href="/services" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg transition shadow-md hover:shadow-lg w-full sm:w-auto">
                        {{ $t('hero.cta_services') }}
                    </Link>
                    <button @click="openBookingModal" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50 md:py-4 md:text-lg transition shadow-sm hover:shadow-md w-full sm:w-auto">
                        {{ $t('hero.book_now') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Featured Services Section -->
        <div class="py-16 bg-white dark:bg-secondary-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white">{{ $t('services.title') }}</h2>
                    <p class="mt-4 text-lg text-secondary-500 dark:text-secondary-400">{{ $t('services.subtitle') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="service in featuredServices" :key="service.id" class="h-full">
                        <ServiceCard :service="service" />
                    </div>
                </div>
                
                <div class="text-center mt-12">
                     <Link href="/services" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium">{{ $t('services.view_all') }} &rarr;</Link>
                </div>
            </div>
        </div>

        <!-- About/Philosophy Section -->
        <div class="py-16 bg-secondary-50 dark:bg-secondary-950 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <div class="mb-10 lg:mb-0">
                        <h2 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white mb-6">{{ $t('philosophy.title') }}</h2>
                        <p class="text-lg text-secondary-600 dark:text-secondary-300 mb-6">
                            {{ $t('philosophy.p1') }}
                        </p>
                        <p class="text-lg text-secondary-600 dark:text-secondary-300">
                            {{ $t('philosophy.p2') }}
                        </p>
                    </div>
                    <div class="relative h-64 sm:h-72 md:h-96 lg:h-full">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary-200 to-secondary-200 dark:from-secondary-800 dark:to-secondary-700 rounded-2xl shadow-inner flex items-center justify-center">
                            <span class="text-secondary-400 dark:text-secondary-600 text-6xl opacity-20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Section -->
        <GallerySection :categories="galleryCategories" />

        <!-- Latest Blog Posts -->
        <div class="py-16 bg-white dark:bg-secondary-900 transition-colors duration-300">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white">{{ $t('blog.title') }}</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="post in latestPosts" :key="post.id" class="group cursor-pointer">
                        <div class="bg-secondary-100 dark:bg-secondary-800 h-48 rounded-lg mb-4 overflow-hidden relative">
                             <img v-if="post.image" :src="post.image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                             <div v-else class="w-full h-full bg-secondary-200 dark:bg-secondary-700 group-hover:scale-105 transition duration-500 flex items-center justify-center text-secondary-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                             </div>
                        </div>
                        <div class="text-sm text-primary-600 dark:text-primary-400 mb-2">{{ new Date(post.published_at).toLocaleDateString() }}</div>
                        <h3 class="text-xl font-bold text-secondary-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition mb-2">{{ post.title }}</h3>
                        <p class="text-secondary-600 dark:text-secondary-400 line-clamp-2">{{ post.excerpt || post.content.replace(/<[^>]*>/g, '') }}</p>
                        <Link :href="`/blog/${post.slug}`" class="inline-block mt-3 text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium text-sm">{{ $t('blog.read_article') }} &rarr;</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div v-if="isBookingModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-secondary-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeBookingModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-secondary-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-secondary-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-secondary-900 dark:text-white" id="modal-title">
                                    {{ $t('booking.select_service_type') }}
                                </h3>
                                <div class="mt-6 space-y-3">
                                    <Link href="/booking/individual" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                        {{ $t('booking.individual_therapy') }}
                                    </Link>
                                    <Link href="/booking/group?type=couples" class="w-full flex justify-center py-3 px-4 border border-secondary-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-secondary-700 dark:text-secondary-200 bg-white dark:bg-secondary-700 hover:bg-secondary-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                        {{ $t('booking.couples_therapy') }}
                                    </Link>
                                    <Link href="/booking/group?type=family" class="w-full flex justify-center py-3 px-4 border border-secondary-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-secondary-700 dark:text-secondary-200 bg-white dark:bg-secondary-700 hover:bg-secondary-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                        {{ $t('booking.family_therapy') }}
                                    </Link>
                                    <Link href="/booking/group?type=group" class="w-full flex justify-center py-3 px-4 border border-secondary-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-secondary-700 dark:text-secondary-200 bg-white dark:bg-secondary-700 hover:bg-secondary-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                        {{ $t('booking.group_therapy') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-secondary-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeBookingModal">
                            {{ $t('common.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
