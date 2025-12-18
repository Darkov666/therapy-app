<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    product: Object,
});

const page = usePage();
const categories = computed(() => {
    return (page.props.categories || []).filter(c => c.type === 'product');
});

const form = useForm({
    _method: 'PUT',
    title: props.product.title,
    description: props.product.description,
    category_id: props.product.category_id,
    price_mxn: props.product.price_mxn,
    type: props.product.type,
    is_active: !!props.product.is_active,
    cover_image: null,
    product_file: null,
});

const standardTypes = ['ebook', 'video', 'audio', 'manual'];
const selectedType = ref('ebook');
const customType = ref('');

// Initialize based on existing product type
if (standardTypes.includes(props.product.type)) {
    selectedType.value = props.product.type;
} else {
    selectedType.value = 'other';
    customType.value = props.product.type;
}

watch(selectedType, (val) => {
    if (val !== 'other') {
        form.type = val;
    } else {
        form.type = customType.value;
    }
});

watch(customType, (val) => {
    if (selectedType.value === 'other') {
        form.type = val;
    }
});

const submit = () => {
    form.post(route('admin.products.update', props.product.id));
};
</script>

<template>
    <Head title="Editar Producto" />

    <AdminLayout title="Editar Producto">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Editar Producto</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Modifica los detalles del producto.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form @submit.prevent="submit">
                        <div class="px-4 py-5 bg-white dark:bg-secondary-800 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="grid grid-cols-6 gap-6">
                                <!-- Title -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="title">
                                        Título
                                    </label>
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                    />
                                    <div v-if="form.errors.title" class="text-red-600 text-sm mt-2">{{ form.errors.title }}</div>
                                </div>

                                <!-- Category -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="category">
                                        Categoría
                                    </label>
                                    <select
                                        id="category"
                                        v-model="form.category_id"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                    >
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.category_id" class="text-red-600 text-sm mt-2">{{ form.errors.category_id }}</div>
                                </div>

                                <!-- Type -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="type_select">
                                        Tipo de Producto
                                    </label>
                                    <select
                                        id="type_select"
                                        v-model="selectedType"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    >
                                        <option value="ebook">Ebook (PDF/EPUB)</option>
                                        <option value="video">Video</option>
                                        <option value="audio">Audio</option>
                                        <option value="manual">Manual</option>
                                        <option value="other">Otro (Especifique)</option>
                                    </select>
                                    
                                     <!-- Dynamic Custom Type Input -->
                                    <div v-if="selectedType === 'other'" class="mt-2">
                                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="custom_type">
                                            Especifique el tipo
                                        </label>
                                        <input
                                            id="custom_type"
                                            v-model="customType"
                                            type="text"
                                            class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                            placeholder="Ej. Software, Plantilla..."
                                            required
                                        />
                                    </div>
                                    <div v-if="form.errors.type" class="text-red-600 text-sm mt-2">{{ form.errors.type }}</div>
                                </div>

                                <!-- Price -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="price">
                                        Precio (MXN)
                                    </label>
                                    <input
                                        id="price"
                                        v-model="form.price_mxn"
                                        type="number"
                                        step="0.01"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        required
                                    />
                                    <div v-if="form.errors.price_mxn" class="text-red-600 text-sm mt-2">{{ form.errors.price_mxn }}</div>
                                </div>

                                <!-- Description -->
                                <div class="col-span-6">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="description">
                                        Descripción
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        class="border-gray-300 dark:border-gray-700 dark:bg-secondary-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-600 text-sm mt-2">{{ form.errors.description }}</div>
                                </div>

                                <!-- Cover Image -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="cover_image">
                                        Portada (Dejar vacio para mantener actual)
                                    </label>
                                    <div v-if="product.cover_url" class="mt-2 mb-2">
                                        <img :src="product.cover_url" class="h-20 rounded-md shadow-sm" />
                                    </div>
                                    <input
                                        id="cover_image"
                                        type="file"
                                        @input="form.cover_image = $event.target.files[0]"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                        accept="image/*"
                                    />
                                    <div v-if="form.errors.cover_image" class="text-red-600 text-sm mt-2">{{ form.errors.cover_image }}</div>
                                </div>

                                <!-- Product File -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="product_file">
                                        Archivo del Producto (Dejar vacio para mantener actual)
                                    </label>
                                    <div v-if="product.file_path" class="mt-2 mb-2">
                                        <span class="text-xs text-green-600 font-semibold">Archivo actual cargado: {{ product.file_path.split('/').pop() }}</span>
                                    </div>
                                    <input
                                        id="product_file"
                                        type="file"
                                        @input="form.product_file = $event.target.files[0]"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                    />
                                    <div v-if="form.errors.product_file" class="text-red-600 text-sm mt-2">{{ form.errors.product_file }}</div>
                                </div>

                                <!-- Active Status -->
                                <div class="col-span-6 flex items-center">
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="form.is_active" class="form-checkbox h-5 w-5 text-indigo-600 dark:bg-secondary-900 dark:border-gray-700">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Producto Activo (Visible en tienda)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-secondary-700 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                            <button
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
