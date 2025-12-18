<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    categories: Object,
});

const form = useForm({
    name: '',
    slug: '',
    icon: '',
    type: 'service',
    is_active: true,
});

const editingCategory = ref(null);
const editForm = useForm({
    name: '',
    slug: '',
    icon: '',
    type: 'service',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.categories.store'), {
        onSuccess: () => {
             form.reset();
             // Swal logic remains
        }
    });
};

const startEdit = (category) => {
    editingCategory.value = category;
    editForm.name = category.name;
    editForm.slug = category.slug;
    editForm.icon = category.icon;
    editForm.type = category.type;
    editForm.is_active = !!category.is_active;
};

const cancelEdit = () => {
    editingCategory.value = null;
    editForm.reset();
};

const update = () => {
    editForm.put(route('admin.categories.update', editingCategory.value.id), {
        onSuccess: () => {
            editingCategory.value = null;
            Swal.fire({
                icon: 'success',
                title: 'Categoría Actualizada',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
};

const deleteCategory = (category) => {
     Swal.fire({
        title: '¿Eliminar categoría?',
        text: "Puede afectar a los servicios asociados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.categories.destroy', category.id));
        }
    });
};
</script>

<template>
    <Head title="Gestión de Categorías" />

    <AdminLayout title="Gestión de Categorías">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Create Form -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800 p-6">
                    <h3 class="text-lg font-bold mb-4 text-secondary-900 dark:text-white">Nueva Categoría</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Nombre</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required
                                class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Ej: Talleres"
                            >
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Tipo</label>
                            <select 
                                v-model="form.type" 
                                class="w-full rounded-lg border-primary-300 dark:border-secondary-700 dark:bg-secondary-800 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <option value="service">Servicio</option>
                                <option value="product">Producto</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">{{ form.errors.type }}</div>
                        </div>
                        <button type="submit" :disabled="form.processing" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium shadow-sm disabled:opacity-50">
                            Crear
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="md:col-span-2">
                <div class="bg-white dark:bg-secondary-900 shadow-sm rounded-xl overflow-hidden border border-primary-100 dark:border-secondary-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-secondary-200 dark:divide-secondary-700">
                            <thead class="bg-primary-50 dark:bg-secondary-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-200 dark:divide-secondary-700 bg-white dark:bg-secondary-900">
                                <tr v-for="category in categories.data" :key="category.id" class="hover:bg-primary-50 dark:hover:bg-secondary-800 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="editingCategory?.id === category.id" class="space-y-2">
                                            <input v-model="editForm.name" class="w-full rounded-md border-gray-300 dark:bg-secondary-700 text-sm" placeholder="Nombre">
                                        </div>
                                        <div v-else class="flex items-center">
                                             <i v-if="category.icon" :class="category.icon" class="mr-2 text-primary-500"></i>
                                             <span class="text-sm font-medium text-secondary-900 dark:text-white">{{ category.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                         <div v-if="editingCategory?.id === category.id">
                                            <select v-model="editForm.type" class="rounded-md border-gray-300 dark:bg-secondary-700 text-sm">
                                                <option value="service">Servicio</option>
                                                <option value="product">Producto</option>
                                            </select>
                                         </div>
                                         <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize" :class="category.type === 'product' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'">
                                            {{ category.type === 'product' ? 'Producto' : 'Servicio' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500 dark:text-secondary-400">
                                         <div v-if="editingCategory?.id === category.id">
                                            <input v-model="editForm.slug" class="w-full rounded-md border-gray-300 dark:bg-secondary-700 text-sm" placeholder="Slug">
                                        </div>
                                        <span v-else>{{ category.slug }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                         <div v-if="editingCategory?.id === category.id">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" v-model="editForm.is_active" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                            </label>
                                        </div>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ category.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <template v-if="editingCategory?.id === category.id">
                                            <button @click="update" class="text-green-600 hover:text-green-900">Guardar</button>
                                            <button @click="cancelEdit" class="text-gray-600 hover:text-gray-900">Cancelar</button>
                                        </template>
                                        <template v-else>
                                            <button @click="startEdit(category)" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <div class="px-6 py-4 border-t border-secondary-200 dark:border-secondary-700">
                        <Pagination :links="categories.links" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
