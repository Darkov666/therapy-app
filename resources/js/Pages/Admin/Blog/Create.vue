<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    topics: Array,
    visibilities: Object,
});

const form = useForm({
    title: '',
    topic_id: '',
    new_topic: '',
    content: '',
    excerpt: '',
    image: null,
    visibility: 'public',
    is_published: false,
    published_at: '',
});

const editorRef = ref(null);
const autosaveInterval = ref(null);
const DRAFT_KEY = 'blog_create_draft';

// Format Date for Input
const now = new Date();
now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
// Initialize published_at with current local time (without timezone) for storage consistency
// form.published_at = now.toISOString().slice(0, 16); 
// Update: User might want it empty initially or default to now?
// User said "definir la fecha y hora". Defaulting to empty creates ambiguity.
// Let's Default to NOW for convenience.
form.published_at = now.toISOString().slice(0, 16);

onMounted(() => {
    // Check for draft
    const draft = localStorage.getItem(DRAFT_KEY);
    if (draft) {
        Swal.fire({
             title: 'Borrador encontrado',
             text: '¿Deseas restaurar el último borrador guardado automáticamente?',
             icon: 'info',
             showCancelButton: true,
             confirmButtonText: 'Sí, restaurar',
             cancelButtonText: 'No, descartar'
        }).then((result) => {
             if (result.isConfirmed) {
                 const data = JSON.parse(draft);
                 form.title = data.title || '';
                 form.topic_id = data.topic_id || '';
                 form.new_topic = data.new_topic || '';
                 form.content = data.content || '';
                 form.excerpt = data.excerpt || '';
                 form.visibility = data.visibility || 'public';
                 form.is_published = data.is_published || false;
                 form.published_at = data.published_at || now.toISOString().slice(0, 16);
             } else {
                 localStorage.removeItem(DRAFT_KEY);
             }
        });
    }

    // Autosave every minute
    autosaveInterval.value = setInterval(() => {
        saveDraft();
    }, 60000);
});

onUnmounted(() => {
    if (autosaveInterval.value) clearInterval(autosaveInterval.value);
});

const saveDraft = () => {
    const data = {
        title: form.title,
        topic_id: form.topic_id,
        new_topic: form.new_topic,
        content: form.content,
        excerpt: form.excerpt,
        visibility: form.visibility,
        is_published: form.is_published,
        published_at: form.published_at,
        timestamp: new Date().toISOString()
    };
    localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
    console.log('Autosaved to localStorage', new Date().toLocaleTimeString());
};

const submit = () => {
    form.post(route('admin.blog.store'), {
        onSuccess: () => {
            localStorage.removeItem(DRAFT_KEY);
            Swal.fire('Guardado', 'La entrada ha sido creada correctamente.', 'success');
        }
    });
};

const handleEditorImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    axios.post(route('blog.upload-image'), formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(response => {
        const url = response.data.url;
        if (editorRef.value) {
            editorRef.value.addImage(url);
        }
    }).catch(error => {
        console.error(error);
        Swal.fire('Error', 'No se pudo subir la imagen.', 'error');
    });
};

const triggerImageInput = () => {
    document.getElementById('editor-image-input').click();
};
</script>

<template>
    <Head title="Nueva Entrada de Blog" />

    <AdminLayout title="Nueva Entrada de Blog">
        <div class="max-w-4xl mx-auto bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800 p-6">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title & Topic -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Título</label>
                        <input v-model="form.title" type="text" required class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800" placeholder="Título de la entrada">
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Tema</label>
                        <div class="space-y-2">
                             <select v-model="form.topic_id" class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800">
                                <option value="">Selecciona un tema...</option>
                                <option v-for="topic in topics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                                <option value="other">Otro (Crear nuevo)</option>
                            </select>
                            <input v-if="form.topic_id === 'other' || form.new_topic" v-model="form.new_topic" type="text" placeholder="Nombre del nuevo tema" class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 mt-2">
                        </div>
                        <div v-if="form.errors.topic_id" class="text-red-500 text-xs mt-1">{{ form.errors.topic_id }}</div>
                        <div v-if="form.errors.new_topic" class="text-red-500 text-xs mt-1">{{ form.errors.new_topic }}</div>
                    </div>
                </div>

                <!-- Rich Text Editor -->
                <div>
                     <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Contenido</label>
                     <p class="text-xs text-secondary-500 mb-2">Autoguardado local activado.</p>
                     
                     <TiptapEditor ref="editorRef" v-model="form.content">
                        <template #toolbar-extra>
                            <input type="file" id="editor-image-input" class="hidden" accept="image/*" @change="handleEditorImageUpload">
                            <button type="button" @click="triggerImageInput" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Insertar Imagen">
                                <i class="fas fa-image"></i>
                            </button>
                        </template>
                     </TiptapEditor>
                     <div v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</div>
                </div>

                <!-- Excerpt -->
                <div>
                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Resumen (Opcional)</label>
                    <textarea v-model="form.excerpt" rows="2" class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800" placeholder="Breve descripción..."></textarea>
                </div>

                 <!-- Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Portada (Imagen Principal)</label>
                        <input type="file" @input="form.image = $event.target.files[0]" accept="image/*" class="w-full text-sm">
                        <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Visibilidad</label>
                            <select v-model="form.visibility" class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800">
                                <option v-for="(label, key) in visibilities" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>
                        
                        <div>
                             <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Fecha de Publicación</label>
                             <input type="datetime-local" v-model="form.published_at" class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800">
                             <p class="text-xs text-gray-500 mt-1">Si dejas esto vacío, se usará la fecha actual al publicar.</p>
                        </div>

                        <div class="flex items-center pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.is_published" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                <span class="ml-3 text-sm font-medium text-secondary-900 dark:text-secondary-300">
                                    {{ form.is_published ? 'Publicar' : 'No Publicar' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                     <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-lg flex items-center">
                        <i class="fas fa-save mr-2"></i> {{ form.is_published ? 'Guardar y Publicar' : 'Guardar (No Publicar)' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
