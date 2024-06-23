<script setup>
import { ArrowDownTrayIcon, XMarkIcon, ChevronLeftIcon, ChevronRightIcon, DocumentIcon, } from "@heroicons/vue/24/solid";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
import { computed, ref } from 'vue';
import { isImage, isVideo } from '@/Libs/helpers';

const props = defineProps({
    attachments: {
        type: Array,
        required: true,
    },
    index: Number,
    modelValue: Boolean,
})

const modalData = ref({
    dialogTitleText: 'Detalle del Adjunto',
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit('update:index', value)
})

const attachment = computed(() => {
    return props.attachments[currentIndex.value]
})

const emit = defineEmits(['update:modelValue', 'update:index'])

const isFirstAttachmentIndex = () => {
    return currentIndex.value === 0
}

const isLastAttachmentIndex = () => {
    return currentIndex.value === props.attachments.length - 1
}

const prevAttach = () => {
    if (isFirstAttachmentIndex()) return
    currentIndex.value--
}

const nextAttach = () => {
    if (isLastAttachmentIndex()) return
    currentIndex.value++
}

const closeModal = () => {
    show.value = false
}
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-[100]">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                    enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="w-screen h-screen">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="flex flex-col w-full overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl">
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

                                <div class="relative flex items-center justify-center">
                                    <div v-if="isFirstAttachmentIndex()"
                                        class="absolute z-[100] left-0 flex items-center h-full cursor-not-allowed w-14 text-sky-200 bg-black/5">
                                        <ChevronLeftIcon />
                                    </div>
                                    <div v-else @click="prevAttach" title="Previo"
                                        class="absolute z-[100] left-0 flex items-center h-full transition-colors duration-150 cursor-pointer w-14 hover:text-sky-400 hover:bg-black/10 text-sky-200 bg-black/5">
                                        <ChevronLeftIcon />
                                    </div>

                                    <div v-if="isLastAttachmentIndex()"
                                        class="absolute z-[100] right-0 flex items-center h-full cursor-not-allowed w-14 text-sky-200 bg-black/5">
                                        <ChevronRightIcon />
                                    </div>
                                    <div v-else @click="nextAttach" title="Siguiente"
                                        class="absolute z-[100] right-0 flex items-center h-full transition-colors duration-150 cursor-pointer w-14 hover:text-sky-400 hover:bg-black/10 text-sky-200 bg-black/5">
                                        <ChevronRightIcon />
                                    </div>

                                    <!-- <pre>{{ index }}</pre>
                                    <pre>{{ attachments }}</pre> -->
                                    <div class="relative group">
                                        <a
                                            :href="
                                                route('post.download-attachment', attachment)
                                            "
                                            title="Descargar"
                                            class="absolute flex items-center justify-center text-gray-100 transition-all bg-gray-600 rounded opacity-25 cursor-pointer w-14 h-14 group-hover:opacity-75 hover:bg-gray-900 right-9 top-8"
                                        >
                                            <ArrowDownTrayIcon class="w-14 h-14" />
                                        </a>

                                        <div class="flex items-center justify-center w-full h-screen p-4">
                                            <template v-if="isImage(attachment) || isVideo(attachment)">
                                                <img v-if="isImage(attachment)" :src="attachment.url" :alt="attachment.name"
                                                    class="max-w-full max-h-full" />
                                                <video v-if="isVideo(attachment)" :src="attachment.url" controls
                                                    :alt="attachment.name" class="max-w-full max-h-full"></video>
                                            </template>

                                            <template v-else>
                                                <div class="flex flex-col items-center justify-center p-32">
                                                    <DocumentIcon class="w-12 h-12 lg:w-16 lg:h-16" />

                                                    <span class="text-sm lg:text-base">
                                                        {{ attachment.name }}
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
