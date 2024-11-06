<script setup>
import {
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/solid";
import BlogPostIcon from '@/Components/Icons/BlogPost.vue'
import { ref } from "vue";

import PhotoModal from "@/Components/dearbook/Attachment/Modal.vue"

const props = defineProps({
    photos: Array,
})

const showPhotoModal = ref(false)
const currentPhotoToPreview = ref(0)
const routePrefix = 'profile.media'

const openPhotoModal = (index) => {
    currentPhotoToPreview.value = {
        index,
        prefix: routePrefix,
    }
    showPhotoModal.value = true
}

const getPostLink = (attachment) => {
    return route('post.show', {
        user: attachment.attachmentable.post
            ? attachment.attachmentable.post.user.username
            : attachment.attachmentable.user.username,
        id: attachment.attachmentable.post
            ? attachment.attachmentable.post_id
            : attachment.attachmentable.id,
    })
}
</script>

<template>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <template v-for="(attachment, index) of photos">
            <div @click="openPhotoModal(index)" title="Ver en detalle"
                class="relative flex flex-col items-center justify-center text-gray-500 cursor-pointer aspect-square bg-cyan-100 group hover:bg-sky-700/40">
                <a :href="getPostLink(attachment)
                    " title="Acceder a la publicación"
                    class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-12 top-2"
                    @click.stop
                ><BlogPostIcon class-content="w-5 h-5" fill-content="#f3f4f6" /></a>

                <a :href="route(routePrefix + '.download-attachment', attachment)
                    " title="Descargar"
                    class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-2 top-2"
                    @click.stop
                >
                    <ArrowDownTrayIcon class="w-5 h-5" />
                </a>

                <img :src="attachment.url" :alt="attachment.name"
                    class="object-contain w-10/12 aspect-square" />
            </div>
        </template>
    </div>

    <PhotoModal :attachments="photos || []"
        :entity-prefix="currentPhotoToPreview.prefix"
        v-model:index="currentPhotoToPreview.index" v-model="showPhotoModal" />
</template>
