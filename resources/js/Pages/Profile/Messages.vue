<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';


defineProps({
    messages: Array, // Collection of messages with replies
});

const form = useForm({
    message: '',
    parent_id: null,
});

const replyingTo = ref(null);

const startReply = (messageId) => {
    replyingTo.value = messageId;
    form.parent_id = messageId;
    form.message = '';
};

const cancelReply = () => {
    replyingTo.value = null;
    form.reset();
};

const submitReply = () => {
    // We reuse the public contact store? Or a specific profile reply route?
    // Public contact store is for NEW topics mainly but can handle parent_id if we added it to validation/fillable.
    // However, `ContactController@store` validation doesn't include `parent_id`.
    // And it sends "ContactConfirmation" which might be weird for a reply in a thread.
    // Users replying from profile is like a chat.
    // I should add a `reply` method to `ContactController` or handle `parent_id` in `store`.
    // Let's check `ContactController`.
    
    // DECISION: Modify ContactController@store to handle parent_id allow authenticated users to reply.
    // OR: Create a new endpoint `contact.reply`. 
    // Given the task has `reply` in the plan under `ContactController`, I should check if I implemented it.
    // I did NOT implement `reply` in `ContactController`. I only did `index` and `store`.
    // I will add `reply` method to `ContactController` and a route.
    
    form.post(route('contact.reply', form.parent_id), {
        onSuccess: () => cancelReply(),
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Historial de Mensajes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div v-for="thread in messages" :key="thread.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Thread Header (Original Message) -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ thread.subject }}</h3>
                            <p class="text-xs text-gray-500">{{ formatDate(thread.created_at) }}</p>
                            <div class="mt-2 text-gray-700 dark:text-gray-300">
                                {{ thread.message }}
                            </div>
                        </div>

                        <!-- Replies -->
                        <div v-if="thread.replies && thread.replies.length" class="space-y-4 mb-6 pl-4 border-l-2 border-indigo-100 dark:border-indigo-900">
                             <div v-for="reply in thread.replies" :key="reply.id" class="bg-gray-50 dark:bg-gray-700 rounded p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">
                                        {{ reply.user_id === $page.props.auth.user.id ? 'Tú' : 'Psicólogo' }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                                    {{ reply.message }}
                                </div>
                             </div>
                        </div>

                        <!-- Reply Action -->
                        <div v-if="replyingTo !== thread.id">
                            <button @click="startReply(thread.id)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                Responder
                            </button>
                        </div>

                        <!-- Reply Form -->
                        <div v-else class="mt-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <form @submit.prevent="submitReply">
                                <textarea
                                    v-model="form.message"
                                    class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 mb-2"
                                    rows="3"
                                    placeholder="Escribe tu respuesta..."
                                ></textarea>
                                <p v-if="form.errors.message" class="text-sm text-red-600 dark:text-red-400 mb-2">{{ form.errors.message }}</p>
                                <div class="flex justify-end space-x-2">
                                    <button @click="cancelReply" type="button" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">Cancelar</button>
                                    <button
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring focus:ring-primary-300 disabled:opacity-25 transition"
                                    >Enviar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div v-if="messages.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                    No tienes mensajes todavía. <Link :href="route('contact.index')" class="text-indigo-600 underline">Contáctanos</Link>
                </div>

            </div>
        </div>
    </MainLayout>
</template>
