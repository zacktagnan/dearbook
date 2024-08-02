<script setup>
import {
    PaperClipIcon,
} from "@heroicons/vue/24/solid";

import { isImage, isVideo, } from "@/Libs/helpers";

defineProps({
    comment: Object,
    attachments: Array,
    maxFileNameLength: Number,
})

defineEmits(['callOpenAttachmentsModal', 'callPrintFileName'])
</script>

<template>
    <div v-for="(myFile, index) of attachments" class="flex gap-4">
        <div class="flex flex-col">
            <div class="flex gap-2">
                <div class="p-1 border w-fit rounded-2xl">
                    <div @click="$emit('callOpenAttachmentsModal', comment, index, 'post.comment')"
                        title="Ver en detalle" class="overflow-hidden cursor-pointer rounded-2xl">
                        <template v-if="
                            isImage(
                                myFile.file ||
                                myFile
                            ) ||
                            isVideo(
                                myFile.file ||
                                myFile
                            )
                        ">
                            <img v-if="
                                isImage(
                                    myFile.file ||
                                    myFile
                                )
                            " :src="myFile.url" :alt="(
                                myFile.file ||
                                myFile
                            ).name
                                " class="object-fill max-w-60" />
                            <video v-if="
                                isVideo(
                                    myFile.file ||
                                    myFile
                                )
                            " :src="myFile.url" controls :alt="(
                                myFile.file ||
                                myFile
                            ).name
                                " class="object-fill h-20"></video>
                        </template>

                        <template v-else>
                            <div class="flex flex-col items-center justify-center p-1 px-1 bg-cyan-100">
                                <PaperClipIcon class="w-9 h-9 lg:w-11 lg:h-11" />

                                <span class="text-xs text-center lg:text-sm" :title="[
                                    (
                                        myFile.file ||
                                        myFile
                                    ).name.length >
                                        maxFileNameLength
                                        ? (
                                            myFile.file ||
                                            myFile
                                        ).name
                                        : '',
                                ]">
                                    {{ $emit('callPrintFileName', (myFile.file || myFile).name) }}
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div>otro adjunto</div> -->
    </div>
</template>
