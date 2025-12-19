<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import { watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    editable: {
        type: Boolean,
        default: true,
    }
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    editable: props.editable,
    extensions: [
        StarterKit,
        Image,
    ],
    onUpdate: () => {
        emit('update:modelValue', editor.value.getHTML());
    },
});

watch(() => props.modelValue, (newValue) => {
    // Only update if content is different to avoid cursor jumps
    const isSame = editor.value.getHTML() === newValue;
    if (isSame) {
        return;
    }
    editor.value.commands.setContent(newValue, false);
});

// Expose editor for parent to add images
const addImage = (url) => {
    if (url) {
        editor.value.chain().focus().setImage({ src: url }).run();
    }
}

defineExpose({ addImage });
</script>

<template>
    <div v-if="editor" class="border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
        <!-- Toolbar -->
        <div v-if="editable" class="bg-gray-50 dark:bg-gray-900 border-b border-gray-300 dark:border-gray-700 p-2 flex flex-wrap gap-2">
            <button @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('bold') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 font-bold" title="Negrita">
                B
            </button>
            <button @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('italic') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 italic" title="Cursiva">
                I
            </button>
            <button @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('strike') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 line-through" title="Tachado">
                S
            </button>
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            <button @click="editor.chain().focus().setParagraph().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('paragraph') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Párrafo">
                P
            </button>
            <button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('heading', { level: 2 }) }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 font-bold" title="H2">
                H2
            </button>
            <button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('heading', { level: 3 }) }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 font-bold" title="H3">
                H3
            </button>
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            <button @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('bulletList') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Lista">
                • List
            </button>
            <button @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('orderedList') }" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700" title="Lista Numerada">
                1. List
            </button>
            <!-- Image Button Slot or handled by parent -->
            <slot name="toolbar-extra"></slot>
        </div>
        
        <!-- Editor Content -->
        <editor-content :editor="editor" class="p-4 min-h-[300px] prose dark:prose-invert max-w-none focus:outline-none" />
    </div>
</template>

<style>
/* Basic TipTap styles */
.ProseMirror:focus {
    outline: none;
}
</style>
