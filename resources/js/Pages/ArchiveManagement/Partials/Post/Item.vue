<script setup>
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { MenuItem } from "@headlessui/vue";
import {
    TrashIcon,
    ArrowUturnLeftIcon,
    ArchiveBoxIcon,
} from "@heroicons/vue/24/solid";
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

// Para versión anterior de OptionsDropDown
// const itemType = 'post'

const isPostAuthor = computed(
    () => authUser && authUser.id === props.post.user.id
);

const isPostGroupAdmin = computed(
    () => props.post.group?.current_group_user?.role === 'admin'
);

const isPostGroupMember = computed(
    () => props.post.group && props.post.group?.current_group_user?.status === 'approved'
);

const disableOptions = computed(() => {
    if (props.post.group) {
        return !isPostGroupMember.value
    }
    return false
});

const isTrashed = computed(
    () => props.post.deleted_at !== null
);

const isTrashedByAuthUser = computed(
    () => authUser && authUser.id === props.post.deleted_by
);

const isPostAuthorOrIsPostGroupAdmin = computed(
    () => isPostAuthor.value || isPostGroupAdmin.value
);

const isTrashedByAdminGroup = computed(
    () => !!(props.post.group && props.post.deleted_by && props.post.deleted_by !== props.post.user_id)
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
        imagePreviewData = getImagePreviewData(post.attachments[0])
        image = imagePreviewData[0]
        isImageFile = imagePreviewData[1]
    } else {
        image = post.user.avatar_path
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
    <div class="relative">
        <div class="flex items-center justify-between py-2 gap-7" :class="index > 0 ? 'border-t border-slate-300' : ''">
            <div class="flex items-center">
                <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all" :class="[
                    isTrashedByAdminGroup
                    ? 'hover:bg-transparent'
                    : 'hover:bg-sky-200'
                ]">
                    <input type="checkbox" :value="post.id" v-model="checkedId" @change="$emit('callCheckItem')"
                        :disabled="isTrashedByAdminGroup" :title="isTrashedByAdminGroup ? 'Imposible de marcar' : ''"
                        class="w-[22px] h-[22px] rounded group-hover/check_all:bg-sky-200 checked:bg-sky-300 focus:checked:bg-sky-300 focus:ring-0 focus:ring-offset-0 disabled:border-gray-300 disabled:hover:bg-gray-200" :class="[
                        isTrashedByAdminGroup
                        ? 'group-hover/check_all:bg-transparent'
                        : 'group-hover/check_all:bg-sky-200'
                    ]">
                </div>
            </div>

            <div class="w-full p-2 rounded-lg hover:bg-black/5 text-sm">
                <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                    class="flex flex-col md:flex-row md:items-center w-full gap-2 p-1">
                <div class="flex items-center gap-2">
                    <img :src="loadImage(post)" class="w-[60px] h-[60px] border-2 rounded-full" />
                    <div class="text-left">
                        <div v-html="getContentExcerpt(post.body) || '(sin texto)'"></div>
                        <small class="lowercase">{{ post.created_at_time }}</small>
                    </div>
                </div>
                <div class="md:ml-8">
                    <div v-if="post.group">
                        <Link :href="route('group.profile', { group: post.group.slug })" title="Acceder al grupo"
                            class="px-2 py-1 rounded-full text-gray-500 hover:text-gray-700 font-bold bg-sky-200 hover:bg-sky-300 transition-colors duration-150">
                        {{ post.group.name }}
                        </Link>
                    </div>
                    <div v-if="isTrashedByAdminGroup" class="mt-2">
                        <div
                            title="Solo un ADMIN de grupo podrá restaurar este Post"
                            class="px-2 py-1.5 rounded-full text-gray-500 hover:text-gray-700 font-bold transition-colors duration-150 leading-none"
                            :class="[
                                isTrashedByAuthUser
                                ? 'bg-orange-200 hover:bg-orange-300'
                                : 'bg-red-200 hover:bg-red-300'
                            ]">
                            <p v-if="isTrashedByAuthUser">Lo borraste como ADMIN de grupo</p>
                            <p v-else>Borrado por ADMIN de grupo</p>
                            <span class="text-xs italic">({{ post.deleted_at_time }})</span>
                        </div>
                    </div>
                </div>
                </Link>
            </div>

            <div class="pr-2">
                <OptionsDropDown v-model="isPostAuthorOrIsPostGroupAdmin" :ellipsis-type-icon="'vertical'" :is-disabled="disableOptions">
                    <template v-if="!isTrashed && !isArchived">
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callSubmitProcess', 'archive', post.id)" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Mover al archivo">
                                <ArchiveBoxIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Al archivo
                            </button>
                            </MenuItem>

                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callSubmitProcess', 'delete', post.id)" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Mover a la papelera">
                                <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                A la papelera
                            </button>
                            </MenuItem>
                        </div>
                    </template>
                    <template v-else>
                        <template v-if="isTrashed">
                            <div v-if="!isTrashedByAdminGroup" class="px-1 py-1">
                                <MenuItem v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'restore_from_trash', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Restaurar publicación">
                                    <ArrowUturnLeftIcon :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                        aria-hidden="true" />
                                    Restaurar
                                </button>
                                </MenuItem>
                            </div>
                            <div v-else-if="isPostGroupAdmin" class="px-1 py-1">
                                <MenuItem v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'restore_from_trash', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Restaurar publicación">
                                    <ArrowUturnLeftIcon :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                        aria-hidden="true" />
                                    Restaurar
                                </button>
                                </MenuItem>
                            </div>

                            <div class="px-1 py-1">
                                <MenuItem v-if="!isTrashedByAdminGroup" v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'archive', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Mover al archivo">
                                    <ArchiveBoxIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                    Al archivo
                                </button>
                                </MenuItem>

                                <MenuItem v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'force_delete', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Eliminar del todo">
                                    <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                    Eliminar
                                </button>
                                </MenuItem>
                            </div>
                        </template>
                        <template v-if="isArchived">
                            <div class="px-1 py-1">
                                <MenuItem v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'restore_from_archive', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Restaurar publicación">
                                    <ArrowUturnLeftIcon :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                        aria-hidden="true" />
                                    Restaurar
                                </button>
                                </MenuItem>
                            </div>

                            <div class="px-1 py-1">
                                <MenuItem v-slot="{ active }">
                                <button @click="$emit('callSubmitProcess', 'delete', post.id)" :class="[
                                    active
                                        ? 'bg-sky-100'
                                        : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Mover a la papelera">
                                    <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                    A la papelera
                                </button>
                                </MenuItem>
                            </div>
                        </template>
                    </template>
                </OptionsDropDown>
            </div>
        </div>

        <!-- <div class="absolute top-0 left-0 w-full h-full bg-black/40 z-10 rounded-lg flex justify-center items-center text-gray-100 italic font-bold">
            <p class="px-4 py-2 rounded-lg bg-gray-700/40 text-justify">Imposible gestionar este Post por haber dejado de ser miembro de este grupo.</p>
        </div> -->
    </div>
</template>
