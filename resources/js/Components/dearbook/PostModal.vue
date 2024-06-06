<script setup>
import PostHeader from '@/Components/dearbook/PostHeader.vue'
// import TextareaInput from '@/Components/TextareaInput.vue';
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { computed } from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
import { useForm } from '@inertiajs/vue3';
import { watch, ref } from 'vue';

import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import '@ckeditor/ckeditor5-build-classic/build/translations/es';

const editor = ClassicEditor
const editorConfig = {
    language: 'es',
    toolbar: {
        items: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            '|',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'outdent',
            'indent',
            'blockquote',
        ],
    },
}

const onEditorReady = (editor) => {
    // Situando el cursor al final del contenido existente...
    editor.model.change(writer => {
        writer.setSelection(editor.model.document.getRoot(), 'end')
    })
    // Poniendo el foco en el editor...
    editor.editing.view.focus()
}

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    modelValue: Boolean
})

const postForm = useForm({
    id: null,
    body: '',
})

const modalData = ref({
    dialogTitleText: 'Crear publicación',
    submitButtonText: 'Publicar',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const emit = defineEmits('update:modelValue')

watch(() => props.post, () => {
    console.log('POST has changed...')
    postForm.id = props.post.id
    postForm.body = props.post.body

    modalData.value.dialogTitleText = 'Editar publicación'
    modalData.value.submitButtonText = 'Actualizar'
}, {
    deep: true,
})

const closeModal = () => {
    show.value = false
}

const submitPostUpdate = () => {
    if (postForm.id) {
        postForm.put(route('post.update', props.post), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
                postForm.reset()
            },
        })
    } else {
        postForm.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
                postForm.reset()
            },
        })
    }
}
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                    enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-full p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="w-full max-w-md overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl">
                                <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300">
                                    <div class="w-full text-center">
                                        <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                            {{ modalData.dialogTitleText }}
                                        </DialogTitle>
                                    </div>

                                    <button @click="closeModal"
                                        class="flex items-center p-1 font-bold text-gray-500 transition-colors duration-200 bg-gray-200 rounded-full hover:bg-gray-300"
                                        title="Cerrar">
                                        <XMarkIcon class="w-5 h-5" />
                                    </button>
                                </div>

                                <div class="px-[14px] pt-[14px]">
                                    <PostHeader :post="post" :show-post-date="false" />
                                    <ckeditor :editor="editor" @ready="onEditorReady" v-model="postForm.body"
                                        :config="editorConfig">
                                    </ckeditor>
                                    <!-- <TextareaInput placeholder="Expresa lo que quieras comunicar" class="w-full mt-2"
                                        v-model="postForm.body" autofocus></TextareaInput> -->
                                </div>

                                <div class="flex p-[14px]">
                                    <button type="button" title="Actualizar"
                                        class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-indigo-900 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                        @click="submitPostUpdate">
                                        {{ modalData.submitButtonText }}
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
