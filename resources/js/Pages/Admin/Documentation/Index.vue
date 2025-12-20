<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const activeTab = ref('admin'); // 'admin', 'client', 'operational'

const tabs = [
    { id: 'admin', name: 'Panel Administrativo' },
    { id: 'client', name: 'Aplicación de Usuario' },
    { id: 'operational', name: 'Detalles Operativos' },
];

const adminSections = [
    {
        title: 'Gestión de Usuarios',
        description: 'Permite visualizar, editar y aprobar cuentas de usuarios (incluyendo psicólogos). Explica cómo cambiar roles y estados.',
        mediaType: 'image', // placeholder
    },
    {
        title: 'Gestión de Servicios y Productos',
        description: 'Cómo agregar, editar y eliminar servicios de terapia y productos de la tienda. Incluye gestión de precios y categorías.',
        mediaType: 'video', // placeholder
    },
    {
        title: 'Gestión del Blog',
        description: 'Creación y edición de artículos para el blog. Uso del editor de texto y subida de imágenes.',
        mediaType: 'image',
    },
    {
        title: 'Configuración del Sistema',
        description: 'Ajustes generales de la plataforma, información de contacto y otras configuraciones globales.',
        mediaType: 'image',
    },
    {
        title: 'Mensajes y Contacto',
        description: 'Cómo visualizar y responder a los mensajes enviados desde el formulario de contacto.',
        mediaType: 'image',
    }
];

const clientSections = [
    {
        title: 'Registro e Inicio de Sesión',
        description: 'Proceso de creación de cuenta para clientes y psicólogos. Verificación de correo y aprobación de cuenta.',
        mediaType: 'video',
    },
    {
        title: 'Reserva de Citas',
        description: 'Flujo completo para agendar una cita: selección de servicio, profesional, fecha/hora y pago.',
        mediaType: 'video',
    },
    {
        title: 'Tienda y Compras',
        description: 'Navegación por la tienda, carrito de compras y proceso de pago (Checkout). Descarga de productos digitales.',
        mediaType: 'image',
    },
    {
        title: 'Panel de Usuario (Dashboard)',
        description: 'Visualización de citas próximas, historial de compras y acceso rápido a funciones principales.',
        mediaType: 'image',
    },
    {
        title: 'Perfil y Seguridad',
        description: 'Actualización de datos personales, foto de perfil y cambio de contraseña.',
        mediaType: 'image',
    }
];
</script>

<template>
    <AdminLayout title="Documentación del Sistema">
        <Head title="Documentación" />

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-secondary-700 mb-8">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            activeTab === tab.id
                                ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg'
                        ]"
                    >
                        {{ tab.name }}
                    </button>
                </nav>
            </div>

            <!-- Operational Details Content -->
            <div v-if="activeTab === 'operational'" class="space-y-12">
                <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow sm:rounded-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 dark:border-secondary-700">1. Tipos de Usuarios y Permisos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-primary-600 dark:text-primary-400 mb-2">Administrador (SuperAdmin)</h3>
                            <ul class="list-disc pl-5 text-gray-600 dark:text-gray-300 space-y-1">
                                <li>Acceso total a todas las funcionalidades del sistema.</li>
                                <li>Gestión completa de usuarios (crear, editar, eliminar, aprobar).</li>
                                <li>Acceso a Configuración Global del sitio.</li>
                                <li>Gestión de Servicios, Productos, Blog y Categorías.</li>
                                <li>Visualización y respuesta de mensajes de contacto.</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-primary-600 dark:text-primary-400 mb-2">Psicólogo</h3>
                            <ul class="list-disc pl-5 text-gray-600 dark:text-gray-300 space-y-1">
                                <li>Acceso al Panel Administrativo (con permisos limitados si se configura).</li>
                                <li>Gestión de su disponibilidad y agenda (si aplica).</li>
                                <li>Visualización de citas asignadas.</li>
                                <li>Puede no tener acceso a la Configuración Global del sistema.</li>
                            </ul>
                        </div>
                         <div>
                            <h3 class="text-lg font-semibold text-primary-600 dark:text-primary-400 mb-2">Usuario (Cliente)</h3>
                            <ul class="list-disc pl-5 text-gray-600 dark:text-gray-300 space-y-1">
                                <li>Acceso exclusivo al "Panel de Usuario" (Frontend).</li>
                                <li>Puede reservar citas y comprar productos.</li>
                                <li>Puede gestionar su perfil y ver su historial de citas/compras.</li>
                                <li>NO tiene acceso al Panel Administrativo.</li>
                            </ul>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 dark:border-secondary-700 pt-8">2. Panel de Usuario (Clientes)</h2>
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Dashboard (Inicio)</h4>
                            <p class="text-gray-600 dark:text-gray-300">Vista resumen con las próximas citas programadas y accesos rápidos.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Agendar Cita (Booking)</h4>
                            <p class="text-gray-600 dark:text-gray-300">Asistente paso a paso para reservar terapia. Permite seleccionar tipo de terapia (Individual, Pareja, etc.), modalidad (Online/Presencial), profesional y métodos de pago.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Tienda (Shop)</h4>
                            <p class="text-gray-600 dark:text-gray-300">Catálogo de productos (físicos o digitales). Permite agregar al carrito, proceder al pago (Checkout) y descargar archivos digitales tras la compra.</p>
                        </div>
                         <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Perfil</h4>
                            <p class="text-gray-600 dark:text-gray-300">Gestión de datos personales: Nombre, contacto, foto de perfil y cambio de contraseña. Historial de sesiones activas (seguridad).</p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 dark:border-secondary-700 pt-8">3. Panel Administrativo</h2>
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Dashboard</h4>
                            <p class="text-gray-600 dark:text-gray-300">Resumen estadístico del sistema (ventas, citas, nuevos usuarios).</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Usuarios</h4>
                            <p class="text-gray-600 dark:text-gray-300">Tabla maestra de todos los usuarios registrados. Permite filtrar por rol, ver detalles, aprobar cuentas de psicólogos pendientes y deshabilitar usuarios.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Servicios y Productos</h4>
                            <p class="text-gray-600 dark:text-gray-300">CRUD (Crear, Leer, Actualizar, Borrar) para los items que se ofrecen en la web. Control de precios, descripciones, imágenes y stock.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Blog</h4>
                            <p class="text-gray-600 dark:text-gray-300">Gestor de contenido para publicar artículos educativos o noticias. Incluye editor de texto enriquecido.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Configuración</h4>
                            <p class="text-gray-600 dark:text-gray-300">Ajustes globales: Nombre del sitio, logo, enlaces a redes sociales, información legal y de contacto.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Content -->
            <div v-if="activeTab === 'admin'" class="space-y-12">
                <div v-for="(section, index) in adminSections" :key="index" class="bg-white dark:bg-secondary-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ section.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">{{ section.description }}</p>
                        
                        <!-- Media Placeholder -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-secondary-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400 dark:border-secondary-600 h-64">
                            <div class="text-center">
                                <svg v-if="section.mediaType === 'video'" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-500">
                                    Inserte aquí {{ section.mediaType === 'video' ? 'el video demostrativo' : 'la captura de pantalla' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Content -->
            <div v-if="activeTab === 'client'" class="space-y-12">
                <div v-for="(section, index) in clientSections" :key="index" class="bg-white dark:bg-secondary-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ section.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">{{ section.description }}</p>

                         <!-- Media Placeholder -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-secondary-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400 dark:border-secondary-600 h-64">
                             <div class="text-center">
                                <svg v-if="section.mediaType === 'video'" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-500">
                                    Inserte aquí {{ section.mediaType === 'video' ? 'el video demostrativo' : 'la captura de pantalla' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
