<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
    name: user ? user.name : '',
    email: user ? user.email : '',
    phone: user ? user.phone : '',
    subject: '',
    message: '',
    use_nickname: false,
});

const submit = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('subject', 'message'),
    });
};
</script>

<template>
    <MainLayout>
        <Head title="Contactanos" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-12">
                        <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-secondary-100 mb-6">
                            Contáctanos
                        </h1>
                        <p class="text-secondary-600 dark:text-secondary-400 mb-8">
                            Estamos aquí para ayudarte. Si tienes alguna pregunta o necesitas asistencia, no dudes en escribirnos por WhatsApp o llenar el formulario.
                        </p>

                        <!-- Contact Options Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            
                            <!-- WhatsApp Section -->
                            <div class="bg-primary-50 dark:bg-secondary-700 p-8 rounded-2xl flex flex-col items-center justify-center text-center">
                                <h2 class="text-2xl font-bold text-primary-700 dark:text-primary-300 mb-4">Chat en WhatsApp</h2>
                                <p class="text-secondary-600 dark:text-secondary-300 mb-6">
                                    Respuesta rápida y directa. ¡Hablemos ahora!
                                </p>
                                <a 
                                    href="https://wa.me/1234567890" 
                                    target="_blank" 
                                    class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-full transition-transform transform hover:scale-105"
                                >
                                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Abrir WhatsApp
                                </a>
                            </div>

                            <!-- Email Form Section -->
                            <div>
                                <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">Envíanos un Correo</h2>
                                <form @submit.prevent="submit" class="space-y-4">
                                    
                                    <!-- Name Field (Conditional) -->
                                    <div v-if="!user">
                                        <label for="name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Nombre</label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                            required
                                            autofocus
                                        />
                                        <p v-if="form.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.name }}</p>
                                    </div>
                                    <div v-else class="flex items-center justify-between mb-4">
                                        <span class="text-secondary-700 dark:text-secondary-300">
                                            Enviando como: 
                                            <strong v-if="!form.use_nickname">{{ user.name }}</strong>
                                            <strong v-else>{{ user.nickname || user.name }}</strong>
                                        </span>
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" v-model="form.use_nickname" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                            <span class="text-sm text-secondary-600 dark:text-secondary-400">Usar Nickname</span>
                                        </label>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Correo Electrónico</label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                            required
                                        />
                                        <p v-if="form.errors.email" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.email }}</p>
                                    </div>

                                    <!-- Phone (Optional) -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Teléfono (Opcional)</label>
                                        <input
                                            id="phone"
                                            v-model="form.phone"
                                            type="tel"
                                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                        />
                                        <p v-if="form.errors.phone" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.phone }}</p>
                                    </div>

                                    <!-- Subject -->
                                    <div>
                                        <label for="subject" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Asunto</label>
                                        <input
                                            id="subject"
                                            v-model="form.subject"
                                            type="text"
                                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                            required
                                        />
                                        <p v-if="form.errors.subject" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.subject }}</p>
                                    </div>

                                    <!-- Message -->
                                    <div>
                                        <label for="message" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Mensaje</label>
                                        <textarea
                                            id="message"
                                            v-model="form.message"
                                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                            required
                                            rows="4"
                                        ></textarea>
                                        <p v-if="form.errors.message" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.message }}</p>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <button 
                                            :disabled="form.processing"
                                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring focus:ring-primary-300 disabled:opacity-25 transition"
                                            :class="{ 'opacity-25': form.processing }"
                                        >
                                            Enviar Mensaje
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
