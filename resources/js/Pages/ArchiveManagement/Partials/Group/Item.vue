<script setup>
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { MenuItem } from "@headlessui/vue";
import {
    ArrowUturnLeftIcon,
} from "@heroicons/vue/24/solid";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    group: Object,
    index: Number,
    isActivityLog: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['callCheckItem', 'callSubmitProcess'])

const authUser = usePage().props.auth.user;

// Para versión anterior de OptionsDropDown
// const itemType = 'group'

const isGroupAuthor = computed(
    () => authUser && authUser.id === props.group.user.id
);

const isTrashed = computed(
    () => props.group.deleted_at !== null
);
// No considerado por el momento
const isArchived = computed(
    // () => props.group.archived_at !== null
    () => false
);

const loadImage = (group) => {
    let image = ''
    let isImageFile = false
    if (group.thumbnail_path) {
        image = group.thumbnail_path
        isImageFile = true
    } else if (group.cover_path) {
        image = group.cover_path
        isImageFile = true
    } else {
        image = group.user.avatar_path
        isImageFile = true
    }

    if (image && isImageFile) {
        return 'storage/' + image
    } else if (image && !isImageFile) {
        return image
    } else {
        return usePage().props.defaultAvatarImage
    }
}

const checkedId = defineModel()

const maxGroupAboutLength = 100
const contentExcerpt = ref("");
const getContentExcerpt = (content) => {
    if (content) {
        const ellipsis = content.length > maxGroupAboutLength ? "..." : "";
        contentExcerpt.value =
            content.substring(0, maxGroupAboutLength) + ellipsis;
    }
    return contentExcerpt.value;
}
</script>

<template>
    <div class="flex items-center justify-between py-2 gap-7" :class="index > 0 ? 'border-t border-slate-300' : ''">
        <div class="flex items-center">
            <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-sky-200">
                <input type="checkbox" :value="group.id" v-model="checkedId" @change="$emit('callCheckItem')"
                    class="w-[22px] h-[22px] rounded group-hover/check_all:bg-sky-200 checked:bg-sky-300 focus:checked:bg-sky-300 focus:ring-0 focus:ring-offset-0">
            </div>
        </div>

        <div class="w-full p-2 rounded-lg hover:bg-black/5 dark:hover:bg-slate-700 text-sm">
            <!-- <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                class="flex items-center w-full gap-2 p-1"> -->
            <div class="flex items-center w-full gap-2 p-1">
                <img :src="loadImage(group)" class="w-[60px] h-[60px] border-2 rounded-full" />
                <div class="text-left">
                    <div class="font-bold cursor-pointer" v-html="group.name"
                        :title="getContentExcerpt(group.about) || '(sin descripción)'" />
                    <small class="lowercase">{{ group.created_at_time }}</small>
                </div>
            </div>
            <!-- </Link> -->
        </div>

        <div class="pr-2">
            <!-- @callArchiveItem="$emit('callSubmitProcess', 'archive', group.id)"
                @callDeleteItem="$emit('callSubmitProcess', 'delete', group.id)"
                @callRestoreItemFromArchive="$emit('callSubmitProcess', 'restore_from_archive', group.id)"
                @callForceDeleteItem="$emit('callSubmitProcess', 'force_delete', group.id)" -->

            <OptionsDropDown v-model="isGroupAuthor" :ellipsis-type-icon="'vertical'">
                <template v-if="isTrashed">
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <button @click="$emit('callSubmitProcess', 'restore_from_trash', group.id)" :class="[
                                active
                                    ? 'bg-sky-100 dark:bg-slate-500'
                                    : 'text-gray-900 dark:text-gray-400',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Restaurar grupo">
                                <ArrowUturnLeftIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Restaurar
                            </button>
                        </MenuItem>
                    </div>
                </template>
            </OptionsDropDown>
        </div>
    </div>
</template>
