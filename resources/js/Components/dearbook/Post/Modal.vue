<script setup>
import PostHeader from '@/Components/dearbook/Post/Header.vue'
// import TextareaInput from '@/Components/TextareaInput.vue';
import { XMarkIcon, PaperClipIcon, } from "@heroicons/vue/24/solid";
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
import { isImage, isVideo } from '@/Libs/helpers';

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
    link: {
        decorators: {
            openInNewTab: {
                mode: 'manual',
                label: 'Open in a new tab',
                attributes: {
                    target: '_blank',
                    rel: 'noopener noreferrer'
                }
            }
        }
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
    modelValue: Boolean,
})

const postForm = useForm({
    id: null,
    body: '',
    attachments: [],
})

const modalData = ref({
    dialogTitleText: 'Crear publicación',
    submitButtonText: 'Publicar',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const emit = defineEmits(['update:modelValue', 'callActiveShowNotification'])

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
    postForm.reset()
    attachmentFiles.value = []
}

const submitPostUpdate = () => {
    postForm.attachments = attachmentFiles.value.map(myFile => myFile.file)

    if (postForm.id) {
        postForm.put(route('post.update', props.post), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
            },
        })
    } else {
        postForm.post(route('post.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
            },
            onError: () => {
                emit('callActiveShowNotification', postForm.errors)
                // console.log('PROPs.ERRORS', postForm.errors)
            },
        })
    }
}

/**
 * {
 *      file: File,
 *      url: '',
 * }
 */
const attachmentFiles = ref([])

const uploadAttachmentSelected = async (event) => {
    // console.log(event)
    console.log('EVENT-TARGET-FILES', event.target.files)

    for (const file of event.target.files) {
        const myFile = {
            file,
            url: await readFile(file),
        }
        attachmentFiles.value.push(myFile)
    }
    event.target.value = null
    console.log('ATTACHMENT_FILES', attachmentFiles.value)
}

const readFile = async (file) => {
    return new Promise((res, rej) => {
        if (isImage(file) || isVideo(file)) {
            const reader = new FileReader()

            reader.onload = () => {
                res(reader.result)
            }
            reader.onerror = rej

            reader.readAsDataURL(file)
        }
        // else if (isVideo(file)) {
        //     const blobURL = URL.createObjectURL(file)

        //     blobURL.onload = () => {
        //         res(reader.result)
        //     }
        //     blobURL.onerror = rej

        //     reader.readAsDataURL(file)
        // }
         else {
            res(null)
        }
    })
}

const removeFile = (file) => {
    attachmentFiles.value = attachmentFiles.value.filter(f => f !== file)
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
                                class="w-full max-w-lg overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl">
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

                                <div v-if="attachmentFiles.length > 0" class="m-[14px]">
                                    <div class="grid gap-3 mt-1" :class="[
                                        attachmentFiles.length === 1 ? 'grid-cols-1' : 'grid-cols-2 lg:grid-cols-3'
                                    ]">
                                        <template v-for="myFile of attachmentFiles">
                                            <div
                                                class="relative flex flex-col items-center justify-center text-gray-500 aspect-square bg-cyan-100 group">

                                                <button @click="removeFile(myFile)" title="Excluir"
                                                    class="absolute flex items-center justify-center text-gray-100 transition-all bg-gray-300 rounded-full opacity-0 cursor-pointer w-7 h-7 group-hover:opacity-100 hover:bg-gray-400 right-2 top-2">
                                                    <XMarkIcon class="w-5 h-5" />
                                                </button>

                                                <template v-if="isImage(myFile.file) || isVideo(myFile.file)">
                                                    <img v-if="isImage(myFile.file)" :src="myFile.url"
                                                        :alt="myFile.file.name"
                                                        class="object-contain w-10/12 aspect-square" />
                                                    <video v-if="isVideo(myFile.file)" :src="myFile.url" controls
                                                        :alt="myFile.file.name"
                                                        class="object-contain w-10/12 aspect-square"></video>
                                                </template>

                                                <template v-else>
                                                    <PaperClipIcon class="w-12 h-12 lg:w-16 lg:h-16" />

                                                    <span class="text-sm text-center lg:text-base">
                                                        {{ myFile.file.name }}
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    class="flex p-[14px] mx-[14px] mt-[14px] border border-gray-300 rounded-md justify-between items-center">
                                    <span class="font-medium">Añadir a tu publicación</span>

                                    <div>
                                        <button type="button" class="relative group/ico_attach_multimedia">
                                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                class="w-6 h-6">
                                                <path
                                                    d="M432,112V96a48.14,48.14,0,0,0-48-48H64A48.14,48.14,0,0,0,16,96V352a48.14,48.14,0,0,0,48,48H80"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="stroke-linejoin:round;stroke-width:32px">
                                                </path>
                                                <rect x="96" y="128" width="400" height="336" rx="45.99" ry="45.99"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="stroke-linejoin:round;stroke-width:32px">
                                                </rect>
                                                <ellipse cx="372.92" cy="219.64" rx="30.77" ry="30.55"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="stroke-miterlimit:10;stroke-width:32px">
                                                </ellipse>
                                                <path
                                                    d="M342.15,372.17,255,285.78a30.93,30.93,0,0,0-42.18-1.21L96,387.64"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="stroke-linecap:round;stroke-linejoin:round;stroke-width:32px">
                                                </path>
                                                <path d="M265.23,464,383.82,346.27a31,31,0,0,1,41.46-1.87L496,402.91"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="stroke-linecap:round;stroke-linejoin:round;stroke-width:32px">
                                                </path>
                                            </svg>
                                            <input @change="uploadAttachmentSelected" type="file" multiple
                                                class="absolute inset-0 opacity-0 cursor-pointer" title="Foto/video">
                                        </button>
                                    </div>
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
