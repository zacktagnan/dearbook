<script setup>
import {
    ArrowDownTrayIcon,
    DocumentIcon,
} from "@heroicons/vue/24/solid";

import { isImage, isVideo, } from "@/Libs/helpers";

const props = defineProps({
    attachments: Array,
    maxPreviewFiles: Number,
})

defineEmits(['callOpenAttachmentsModal'])

const maxPreviewIndex = props.maxPreviewFiles - 1;
</script>

<template>
    <template v-for="(attachment, index) of attachments.slice(
        0,
        maxPreviewFiles
    )">
        <div @click="$emit('callOpenAttachmentsModal', index)" title="Ver en detalle"
            class="relative flex flex-col items-center justify-center text-gray-500 cursor-pointer aspect-square bg-sky-200 group hover:bg-sky-700/40 dark:bg-cyan-900 dark:hover:bg-cyan-400/40">
            <div v-if="
                index === maxPreviewIndex &&
                attachments.length > maxPreviewFiles
            "
                class="absolute inset-0 flex items-center justify-center text-[24px] md:text-[28px] text-white bg-black/60">
                +{{ attachments.length - maxPreviewIndex }}
            </div>

            <a :href="route('post.download-attachment', attachment)
                " v-if="index < maxPreviewIndex" title="Descargar"
                class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-2 top-2"
                @click.stop
            >
                <ArrowDownTrayIcon class="w-5 h-5" />
            </a>

            <template v-if="isImage(attachment) || isVideo(attachment)">
                <img v-if="isImage(attachment)" :src="attachment.url" :alt="attachment.name"
                    class="object-contain w-10/12 aspect-square" />
                <video v-if="isVideo(attachment)" :src="attachment.url" controls :alt="attachment.name"
                    class="object-contain w-10/12 aspect-square"></video>
            </template>

            <template v-else>
                <div class="flex flex-col items-center justify-center">
                    <DocumentIcon class="w-12 h-12 lg:w-16 lg:h-16" />

                    <span class="text-sm lg:text-base">
                        {{ attachment.name }}
                    </span>
                </div>
            </template>
        </div>
    </template>
</template>
