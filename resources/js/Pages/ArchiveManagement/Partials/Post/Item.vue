<script setup>
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    post: Object,
    index: Number,
    isActivityLog: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['callCheckItem', 'callSubmitProcess'])

const authUser = usePage().props.auth.user;

const itemType = 'post'

const isPostAuthor = computed(
    () => authUser && authUser.id === props.post.user.id
);

const isTrashed = computed(
    () => props.post.deleted_at !== null
);

const isArchived = computed(
    () => props.post.archived_at !== null
);

import { isImage, isVideo, } from "@/Libs/helpers";

const loadImage = (post) => {
    let imagePreviewData = []
    let image = ''
    let isImageFile = false
    if (post.attachments.length > 0) {
        // image = post.attachments[0].path
        imagePreviewData = getImagePreviewData(post.attachments[0])
        image = imagePreviewData[0]
        isImageFile = imagePreviewData[1]
    } else {
        image = post.user.avatar_path
        isImageFile = true
    }

    // if (image) {
    //     return 'storage/' + image
    // }
    // return null

    if (image && isImageFile) {
        return 'storage/' + image
    } else if (image && !isImageFile) {
        return image
    } else {
        return null
    }
}

const getImagePreviewData = (firstAttachment) => {
    let imagePreviewUrl = ''
    let isImageFile = false
    if (isImage(firstAttachment)) {
        imagePreviewUrl = firstAttachment.path
        isImageFile = true
    } else if (isVideo(firstAttachment)) {
        imagePreviewUrl = '/img/default_video_file.png'
    } else {
        imagePreviewUrl = '/img/default_attachment_file.png'
    }
    return [imagePreviewUrl, isImageFile]
}

const checkedId = defineModel()

const maxPostBodyLength = 100
const contentExcerpt = ref("");
const getContentExcerpt = (content) => {
    if (content) {
        const ellipsis = content.length > maxPostBodyLength ? "..." : "";
        contentExcerpt.value =
            content.substring(0, maxPostBodyLength) + ellipsis;
    }
    return contentExcerpt.value;
}
</script>

<template>
    <div class="flex items-center justify-between py-2 gap-7" :class="index > 0 ? 'border-t border-slate-300' : ''">
        <div class="flex items-center">
            <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-sky-200">
                <input type="checkbox" :value="post.id" v-model="checkedId" @change="$emit('callCheckItem')"
                    class="w-[22px] h-[22px] rounded group-hover/check_all:bg-sky-200 checked:bg-sky-300 focus:checked:bg-sky-300 focus:ring-0 focus:ring-offset-0">
            </div>
        </div>

        <div class="w-full p-2 rounded-lg hover:bg-black/5">
            <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                class="flex items-center w-full gap-2 p-1">
            <img :src="loadImage(post) || '/img/default_avatar.png'" class="w-[60px] h-[60px] border-2 rounded-full" />
            <div class="text-sm text-left">
                <div v-html="getContentExcerpt(post.body) || '(sin texto)'"></div>
                <small class="lowercase">{{ post.created_at_time }}</small>
            </div>
            </Link>
        </div>

        <div class="pr-2">
            <OptionsDropDown v-model="isPostAuthor" :is-trashed="isTrashed" :is-archived="isArchived"
                :is-activity-log="isActivityLog" @callArchiveItem="$emit('callSubmitProcess', 'archive', post.id)"
                @callDeleteItem="$emit('callSubmitProcess', 'delete', post.id)"
                @callRestoreItemFromArchive="$emit('callSubmitProcess', 'restore_from_archive', post.id)"
                @callRestoreItemFromTrash="$emit('callSubmitProcess', 'restore_from_trash', post.id)"
                @callForceDeleteItem="$emit('callSubmitProcess', 'force_delete', post.id)"
                :ellipsis-type-icon="'vertical'" :item-type="itemType" />
        </div>
    </div>
</template>
