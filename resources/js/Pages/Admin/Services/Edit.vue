<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: Object,
    categories: Array,
    psychologists: Array,
    assigned_psychologists: Array,
    exchange_rate: [Number, String],
});

const form = useForm({
    _method: 'PUT',
    title: props.service.title,
    description: props.service.description,
    category_id: props.service.category_id,
    type: props.service.type,
    duration_minutes: props.service.duration_minutes,
    price_mxn: props.service.price_mxn,
    psychologists: props.assigned_psychologists,
    image: null,
});

const exchangeRate = parseFloat(props.exchange_rate) || 20;

const priceUsd = computed(() => {
    return (form.price_mxn / exchangeRate).toFixed(2);
});

const submit = () => {
    form.post(route('admin.services.update', props.service.id));
};
</script>

<template>
    <Head title="Editar Servicio" />

    <AdminLayout title="Editar Servicio">
        <div class="max-w-4xl mx-auto bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800 p-8">
            <h2 class="text-xl font-bold mb-6 text-secondary-900 dark:text-white">Editar: {{ service.title }}</h2>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block font-medium text-secondary-900 dark:text-white mb-2">Título del Servicio</label>
                    <input 
                        v-model="form.title" 
                        type="text" 
                        required
                        class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block font-medium text-secondary-900 dark:text-white mb-2">Descripción</label>
                    <textarea 
                        v-model="form.description" 
                        rows="4" 
                        required
                        class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Categoría</label>
                        <select 
                            v-model="form.category_id" 
                            required
                            class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="" disabled>Seleccionar Categoría</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Tipo</label>
                        <select 
                            v-model="form.type" 
                            required
                            class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="individual">Individual</option>
                            <option value="couple">Pareja</option>
                            <option value="family">Familia</option>
                            <option value="group">Grupo</option>
                            <option value="workshop">Taller</option>
                            <option value="special">Especial</option>
                            <option value="ebook">Ebook</option>
                            <option value="video">Video</option>
                            <option value="manual">Manual</option>
                            <option value="audio">Audio</option>
                        </select>
                         <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Duration -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Duración (min)</label>
                        <input 
                            v-model="form.duration_minutes" 
                            type="number" 
                            required
                            class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                        >
                         <div v-if="form.errors.duration_minutes" class="text-red-500 text-sm mt-1">{{ form.errors.duration_minutes }}</div>
                    </div>

                    <!-- Price MXN -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Precio (MXN)</label>
                        <div class="relative rounded-md shadow-sm">
                             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input 
                                v-model="form.price_mxn" 
                                type="number" 
                                step="0.01"
                                required
                                class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 pl-7 focus:ring-primary-500 focus:border-primary-500"
                            >
                        </div>
                         <div v-if="form.errors.price_mxn" class="text-red-500 text-sm mt-1">{{ form.errors.price_mxn }}</div>
                    </div>

                    <!-- Price USD (Calculated) -->
                    <div>
                        <label class="block font-medium text-secondary-900 dark:text-white mb-2">Precio (USD - Aprox)</label>
                        <div class="relative rounded-md shadow-sm">
                             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input 
                                :value="priceUsd" 
                                type="text" 
                                disabled
                                class="w-full rounded-lg border-gray-200 bg-gray-50 dark:border-secondary-700 dark:bg-secondary-800 text-gray-500 pl-7"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Calculado a {{ exchangeRate }} MXN/USD</p>
                    </div>
                </div>

                <!-- Image -->
                <div>
                     <label class="block font-medium text-secondary-900 dark:text-white mb-2">Imagen de Portada (Dejar vacío para mantener actual)</label>
                     <input 
                        type="file" 
                        @input="form.image = $event.target.files[0]"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                    >
                     <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
                </div>

                <!-- Psychologists -->
                <div>
                    <label class="block font-medium text-secondary-900 dark:text-white mb-2">Psicólogos Autorizados</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 border border-primary-100 dark:border-secondary-700 rounded-lg p-4 bg-gray-50 dark:bg-secondary-800">
                        <div v-for="psych in psychologists" :key="psych.id" class="flex items-center">
                            <input 
                                type="checkbox" 
                                :value="psych.id" 
                                v-model="form.psychologists"
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                            <label class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                {{ psych.name }}
                            </label>
                        </div>
                    </div>
                     <p class="text-xs text-secondary-500 mt-1">Seleccione quiénes pueden impartir este servicio.</p>
                </div>

                <div class="flex justify-end pt-4">
                     <button type="button" @click="router.visit(route('admin.services.index'))" class="mr-4 px-6 py-2 border border-secondary-300 text-secondary-700 rounded-lg hover:bg-secondary-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium shadow-sm disabled:opacity-50">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
