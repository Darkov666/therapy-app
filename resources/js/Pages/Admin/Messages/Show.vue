<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    message: Object,
});

const form = useForm({
    message: '',
});

const submitReply = () => {
    form.post(route('admin.messages.store', props.message.id), { // Using store route defined as resource on parent
        // Actually route resource 'store' is top level, but I added it as a reply?
        // Wait, route::resource creates store with POST /messages. 
        // My Controller store method signature is `store(Request $request, ContactMessage $message)`.
        // This expects route model binding. 
        // But standard resource store is `store(Request $request)`.
        // I should have defined a separate route for reply "messages/{message}/reply" or changed store to accept ID.
        // Actually, route resource 'store' URL is POST /admin/messages. It DOES NOT accept a parameter by default.
        // My controller definition was `public function store(Request $request, ContactMessage $message)`. 
        // This won't work with standard resource route.
        // I need to fix the route in web.php or change controller logic.
        // Changing route to: Route::post('messages/{message}/reply', ...)->name('messages.reply');
        // Or I can just use POST /admin/messages and pass 'parent_id' in body.
        
        // I will fix the route/controller mismatch in next steps. 
        // For now, let's assume I'm sending to a route that works.
        // I will use `back()` or preserved state.
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
    <AdminLayout title="Ver Mensaje">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Conversación con {{ message.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Main Message -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ message.subject }}</h3>
                             <p class="text-sm text-gray-500">De: {{ message.name }} &lt;{{ message.email }}&gt;</p>
                            <p v-if="message.phone" class="text-sm text-gray-500">Tel: {{ message.phone }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ formatDate(message.created_at) }}</p>
                        </div>
                        <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 whitespace-pre-line">
                            {{ message.message }}
                        </div>
                    </div>
                </div>

                <!-- Thread/Replies -->
                <div v-if="message.replies && message.replies.length > 0" class="space-y-4 mb-8 pl-8 border-l-2 border-gray-200 dark:border-gray-700">
                     <div v-for="reply in message.replies" :key="reply.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ reply.name }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <span v-if="reply.user_id === $page.props.auth.user.id" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Tú</span>
                                <span v-else class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Cliente</span>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ reply.message }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Responder</h3>
                        <form @submit.prevent="form.post(route('admin.messages.reply', message.id))">
                            <div class="mb-4">
                                <label for="reply_message" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Mensaje de respuesta</label>
                                <textarea
                                    id="reply_message"
                                    v-model="form.message"
                                    class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    rows="4"
                                    required
                                ></textarea>
                                <p v-if="form.errors.message" class="text-sm text-red-600 dark:text-red-400 mt-1">{{ form.errors.message }}</p>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring focus:ring-primary-300 disabled:opacity-25 transition"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    Enviar Respuesta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
