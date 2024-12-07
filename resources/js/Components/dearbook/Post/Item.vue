<script setup>
import { ref, computed } from "vue";
import { ChatBubbleLeftRightIcon, DocumentDuplicateIcon, EyeIcon } from "@heroicons/vue/24/outline"
import PinIcon from '@/Components/Icons/Pin.vue'
import UnpinIcon from '@/Components/Icons/Unpin.vue'
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';

const props = defineProps({
    post: Object,
    typeList: {
        type: String,
        default: 'latest',
    },
    parent_page_name: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'callOpenEditModal',
    'callOpenDetailModal',
    'callOpenAttachmentsModal',
    'callOpenUserReactionsModal',
    'callArchiveItem',
    'callConfirmDeletion',
    'callActiveShowGeneralNotification',
    'callActiveShowNotification',
    'callRestoreItemFromArchive',
    'callRestoreItemFromTrash',
    'callForceDeleteItem',
]);

const maxPostBodyLength = 100;

import { reactionTypesFormat, } from "@/Libs/helpers";

// =======================================================================================

import PostHeader from "@/Components/dearbook/Post/Header.vue";
import UrlPreview from "@/Components/dearbook/Post/UrlPreview.vue"
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { MenuItem } from "@headlessui/vue";
import {
    PencilIcon,
    TrashIcon,
    ArrowUturnLeftIcon,
    ArchiveBoxIcon,
} from "@heroicons/vue/24/solid";
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import PostAttachments from '@/Components/dearbook/Post/Attachments.vue'

// APLICANDO Hash Tags...
// const postBody = computed(() => props.post.body.replace(
//     /(#\p{L}+)(?![^<]*<\/a>)/gu,
//     '<a href="/global-search/$1">$1</a>')
// )
// -> aplicando el "encodeURIComponent" debido al "#"
const postBody = computed(() => props.post.body.replace(
    // cualquier coincidencia de "#texto_seguido"
    // /(#\p{L}+)(?![^<]*<\/a>)/gu,
    // cualquier coincidencia de "#texto_seguido" mientras no estén dentro de una URL
    /(?<!https?:\/\/[^\s]*)(#\p{L}+)(?![^<]*<\/a>)/gu,
    (match, group) => {
        const encodedGroup = encodeURIComponent(group)
        return `<a href="/global-search/${encodedGroup}" class="post-hashtag">${group}</a>`
    })
)

const isTherePreviewData = computed(
    () => !!(props.post.preview && (props.post.preview.title || props.post.preview.image || props.post.preview.description))
)

const openEditModal = () => {
    emit("callOpenEditModal", props.post);
};

const openDetailModal = () => {
    emit("callOpenDetailModal", props.post);
};

// =======================================================================================

const authUser = usePage().props.auth.user;

const isPostAuthor = computed(
    () => authUser && authUser.id === props.post.user.id
);

const isPostGroupAdmin = computed(
    () => props.post?.group?.role === 'admin'
);

// const isPostAuthorOrIsPostGroupAdmin = computed(
//     () => (authUser && authUser.id === props.post.user.id) || (props.post?.group?.role === 'admin')
// );
const isPostAuthorOrIsPostGroupAdmin = computed(
    () => isPostAuthor.value || isPostGroupAdmin.value
);

const isTrashed = computed(
    () => props.post.deleted_at !== ''
);

const { group, user } = props.post;

const isTrashedByAdminGroup = computed(
    () => group && props.post.deleted_by && props.post.deleted_by !== props.post.user.id
);

const isPostGroupMember = computed(() => !!(group && group?.status === 'approved'));
const disableOptions = computed(() => !!(group && !isPostGroupMember.value));
const isPrivateGroup = computed(() => !!(group && group?.type === 'private'))
const isPostGroupMemberOrNotIsPrivateGroup = computed(
    () => isPostGroupMember.value || !isPrivateGroup.value
)

const isArchived = computed(
    () => props.post.archived_at !== ''
);

const maxPreviewFiles = 6;

const openAttachmentPreview = (index) => {
    emit("callOpenAttachmentsModal", props.post, index, 'post');
};

const openCommentAttachmentPreview = (comment, index, entityPrefix) => {
    emit("callOpenAttachmentsModal", comment, index, entityPrefix);
}

import { onMounted, watch } from "vue";

watch(
    () => props.post.all_comments,
    () => {
        // console.log('POST.all_comments - Listado de Comment ha cambiado tras añadir uno nuevo.')
        setCommentsList()
    }
)

const commentsList = ref({})

const setCommentsList = () => {
    switch (props.typeList) {
        case 'latest':
            commentsList.value = props.post.latest_comments
            break;
        case 'all':
            commentsList.value = props.post.all_comments
            break;
        default:
            break;
    }
}

onMounted(() => {
    setTypeUserReactionsPostByType()
    setCommentsList()
});

const setTypeUserReactionsPostByType = () => {
    defaultTabIndex.value = 0
    defaultTabIndexObject.value = {}

    Object.keys(reactionTypesFormat).forEach(type => {
        if (type !== 'all') {
            typeUserReactionsPost(type)
        }
    });
}

const defaultTabIndex = ref(0)
const defaultTabIndexObject = ref({})

const setDefaultTabIndex = (data, type) => {
    if (data.length > 0 || (props.post.current_user_has_reaction && props.post.current_user_type_reaction === type)) {
        defaultTabIndex.value = defaultTabIndex.value + 1
        defaultTabIndexObject.value[type] = defaultTabIndex.value
    }
}

const typeUserReactionsPost = (type) => {
    let data;
    switch (type) {
        case "like":
            data = props.post.like_user_reactions
            break;
        case "love":
            data = props.post.love_user_reactions
            break;
        case "care":
            data = props.post.care_user_reactions
            break;
        case "haha":
            data = props.post.haha_user_reactions
            break;
        case "wow":
            data = props.post.wow_user_reactions
            break;
        case "sad":
            data = props.post.sad_user_reactions
            break;
        case "angry":
            data = props.post.angry_user_reactions
            break;
        default:
            break;
    }
    setDefaultTabIndex(data, type)
}

// ============================================================================

const copyToClipboard = async () => {
    if (!navigator.clipboard) {
        // alert('La API de portapapeles no es compatible con este navegador.')
        alert('La función de copiar al portapapeles no es compatible con este navegador.\n\nTendrás que cambiar de navegador o copiar la URL manualmente.')
        return
    }

    let postUrl = route('post.show', {
        user: props.post.user.username,
        id: props.post.id,
    })

    try {
        await navigator.clipboard.writeText(postUrl)
        // alert('Texto copiado al portapapeles!')
        emit('callActiveShowGeneralNotification', '¡Enlace copiado al portapapeles!')
    } catch (err) {
        console.error('Error al copiar: ', err)
        alert('Error al copiar el enlace')
    }
}
// const copyToClipboard = () => {
//     alert('La API de portapapeles no es compatible con este navegador.')
// }

// ============================================================================

import PostReactionTypeUsersSummary from "@/Components/dearbook/Post/Reaction/TypeUsersSummary.vue";
import PostReactionBox from "@/Components/dearbook/Post/Reaction/Box.vue";

const openUserReactionsModal = (entity, tabIndex) => {
    emit("callOpenUserReactionsModal", entity, tabIndex);
};

const confirmDeletion = (entity, entityPrefix) => {
    emit("callConfirmDeletion", entity, entityPrefix);
}

const activeShowNotification = (errors) => {
    emit("callActiveShowNotification", errors);
};

import PostCommentUsersSummary from '@/Components/dearbook/Comment/UsersSummary.vue'
import PostCommentBox from '@/Components/dearbook/Comment/Box.vue'

const postCommentBoxRef = ref(null)
const focusCommentTextArea = () => {
    postCommentBoxRef.value.focusCommentTextAreaOfCreate()
};

const filterDeletedComment = (commentId) => {
    props.post.all_comments = props.post.all_comments.filter(c => c.id != commentId)
    props.post.latest_comments = props.post.latest_comments.filter(c => c.id != commentId)
    props.post.total_of_comments--
}

const restartGeneralDataFromPostComments = (generalData) => {
    props.post.total_of_comments = generalData.total_of_comments
    props.post.current_user_has_comment = generalData.current_user_has_comment
    props.post.current_user_total_of_comments = generalData.current_user_total_of_comments
}

const restartPostCommentList = (latestComments, allComments) => {
    props.post.latest_comments = latestComments
    props.post.all_comments = allComments
}

//// const isPinned = computed(() => !!(group && group.pinned_post_id === props.post.id) || !!(authUser && authUser.pinned_post_id === props.post.id))
const isPinned = computed(() => {
    // if (group?.id) {
    //     return group?.pinned_post_id === props.post.id
    // }
    // return authUser?.pinned_post_id === props.post.id

    if (props.parent_page_name === 'group_profile') {
        return group.pinned_post_id === props.post.id
    } else if (props.parent_page_name === 'user_profile') {
        return user.pinned_post_id === props.post.id
    }
})

const pinUnpinPost = () => {
    // axiosClient.post(route('post.pin-unpin', props.post.id))
    //     .then((res) => {
    //         props.post.is_pinned = !props.post.is_pinned
    //     })
    const form = useForm({
        // is_pinned_on_group_profile: group?.id ? true : false,
        is_pinned_on_group_profile: props.parent_page_name === 'group_profile' ? true : false,
    })

    form.post(route('post.pin-unpin', props.post.id), {
        // preserveScroll: true,
        // onSuccess: () => {
        //     if (groupPost?.id) {
        //         // groupPost.pinned_post_id = isPostPinned ? null : props.post.id
        //         ////groupPost.pinned_post_id = isPostPinned ? null : usePage().props.success.current_pinned_post_id
        //         // ---------------------------------------------------------------------------------------------------------------
        //         ////console.log('... tras procesar, post marcado como fijado: ', groupPost.pinned_post_id)
        //         ////console.log('usePage().props.success.current_pinned_post_id: ', usePage().props.success.current_pinned_post_id)
        //     }
        // },
    })
}

const updatePinnedState = () => {
    //// if (groupPost?.id) {
    // if (group?.id) {
    //     ////console.log("\n[desde updatePinnedState]\n" + 'usePage().props.success.current_pinned_post_id: ', usePage().props.success.current_pinned_post_id)

    //     ////console.log("\n[desde updatePinnedState]\n" + 'groupPost.pinned_post_id: ', groupPost.pinned_post_id)
    //     //// groupPost.pinned_post_id = usePage().props.success.current_pinned_post_id
    //     group.pinned_post_id = usePage().props.success.current_pinned_post_id
    // } else {
    //     authUser.pinned_post_id = usePage().props.success.current_pinned_post_id
    // }
    if (props.parent_page_name === 'group_profile') {
        group.pinned_post_id = usePage().props.success?.current_pinned_post_id
    } else if (props.parent_page_name === 'user_profile') {
        user.pinned_post_id = usePage().props.success?.current_pinned_post_id
    }
}

defineExpose({
    filterDeletedComment, restartGeneralDataFromPostComments, restartPostCommentList, updatePinnedState,
})
</script>

<template>
    <div class="p-4 mx-0.5 bg-white dark:bg-slate-800 dark:border-slate-900 dark:text-gray-100" :class="[
        typeList === 'latest'
            ? 'mt-4 rounded shadow hover:shadow-cyan-900 dark:hover:shadow-cyan-200'
            : ''
    ]">
        <div class="flex items-center justify-between">
            <PostHeader :post="post" />

            <div class="flex items-center gap-5">
                <div v-if="isPinned" class="flex items-center mb-1">
                    <PinIcon :alt="$tChoice('dearbook.post.index.pinned_state.title', isPinned)" class-content="w-4 h-4 mr-2 text-sky-400 fill-current" aria-hidden="true" />
                    <span class="text-sm">{{ $tChoice('dearbook.post.index.pinned_state.text', isPinned) }}</span>
                </div>
                <OptionsDropDown v-model="isPostGroupMemberOrNotIsPrivateGroup" :ellipsis-type-icon="'vertical'">
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle" @click="openEditModal" :class="[
                                active
                                    ? 'bg-sky-100 dark:bg-slate-500'
                                    : 'text-gray-900 dark:text-gray-400',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]">
                                <EyeIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Abrir detalle
                            </Link>
                        </MenuItem>

                        <MenuItem v-slot="{ active }">
                            <button title="Copiar enlace a portapapeles" @click="copyToClipboard" :class="[
                                active
                                    ? 'bg-sky-100 dark:bg-slate-500'
                                    : 'text-gray-900 dark:text-gray-400',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]">
                                <DocumentDuplicateIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Copiar enlace
                            </button>
                        </MenuItem>
                    </div>
                    <template v-if="isPostAuthorOrIsPostGroupAdmin">
                        <template v-if="isPostAuthor">
                            <template v-if="!isTrashed && !isArchived">
                                <div class="px-1 py-1">
                                    <MenuItem v-if="parent_page_name === 'user_profile' || parent_page_name === 'group_profile'" v-slot="{ active }" as="a">
                                        <button @click="pinUnpinPost" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" :title="$tChoice('dearbook.post.index.options_drop_down.pin_or_unpin.title', (isPinned ? 1 : 0))">
                                            <UnpinIcon v-if="isPinned" :active="active" class-content="w-5 h-5 mr-2 text-sky-400 fill-current" aria-hidden="true" />
                                            <PinIcon v-else :active="active" class-content="w-5 h-5 mr-2 text-sky-400 fill-current" aria-hidden="true" />
                                            {{ $tChoice('dearbook.post.index.options_drop_down.pin_or_unpin.action', (isPinned ? 1 : 0)) }}
                                        </button>
                                    </MenuItem>

                                    <MenuItem v-slot="{ active }">
                                        <button @click="openEditModal" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" title="Editar publicación">
                                            <PencilIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                            Editar
                                        </button>
                                    </MenuItem>
                                </div>

                                <div class="px-1 py-1">
                                    <MenuItem v-slot="{ active }">
                                        <button @click="$emit('callArchiveItem', post.id)" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" title="Mover al archivo">
                                            <ArchiveBoxIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                            Al archivo
                                        </button>
                                    </MenuItem>

                                    <MenuItem v-slot="{ active }">
                                        <button @click="$emit('callConfirmDeletion', post, 'post')" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
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
                                            <button @click="$emit('callRestoreItemFromTrash')" :class="[
                                                active
                                                    ? 'bg-sky-100 dark:bg-slate-500'
                                                    : 'text-gray-900 dark:text-gray-400',
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
                                            <button @click="$emit('callArchiveItem', post.id)" :class="[
                                                active
                                                    ? 'bg-sky-100 dark:bg-slate-500'
                                                    : 'text-gray-900 dark:text-gray-400',
                                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                            ]" title="Mover al archivo">
                                                <ArchiveBoxIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                                Al archivo
                                            </button>
                                        </MenuItem>

                                        <MenuItem v-slot="{ active }">
                                            <button @click="$emit('callForceDeleteItem')" :class="[
                                                active
                                                    ? 'bg-sky-100 dark:bg-slate-500'
                                                    : 'text-gray-900 dark:text-gray-400',
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
                                            <button @click="$emit('callRestoreItemFromArchive')" :class="[
                                                active
                                                    ? 'bg-sky-100 dark:bg-slate-500'
                                                    : 'text-gray-900 dark:text-gray-400',
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
                                            <button @click="$emit('callConfirmDeletion', post, 'post')" :class="[
                                                active
                                                    ? 'bg-sky-100 dark:bg-slate-500'
                                                    : 'text-gray-900 dark:text-gray-400',
                                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                            ]" title="Mover a la papelera">
                                                <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                                A la papelera
                                            </button>
                                        </MenuItem>
                                    </div>
                                </template>
                            </template>
                        </template>

                        <template v-else-if="isPostGroupAdmin">
                            <div v-if="parent_page_name === 'group_profile'" class="px-1 py-1">
                                <MenuItem v-slot="{ active }" as="a">
                                    <button @click="pinUnpinPost" :class="[
                                        active
                                            ? 'bg-sky-100 dark:bg-slate-500'
                                            : 'text-gray-900 dark:text-gray-400',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]" :title="$tChoice('dearbook.post.index.options_drop_down.pin_or_unpin.title', (isPinned ? 1 : 0))">
                                        <UnpinIcon v-if="isPinned" :active="active" class-content="w-5 h-5 mr-2 text-sky-400 fill-current" aria-hidden="true" />
                                        <PinIcon v-else :active="active" class-content="w-5 h-5 mr-2 text-sky-400 fill-current" aria-hidden="true" />
                                        {{ $tChoice('dearbook.post.index.options_drop_down.pin_or_unpin.action', (isPinned ? 1 : 0)) }}
                                    </button>
                                </MenuItem>
                            </div>
                            <template v-if="!isTrashed">
                                <div class="px-1 py-1">
                                    <MenuItem v-slot="{ active }">
                                        <button @click="$emit('callConfirmDeletion', post, 'post')" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" title="Mover a la papelera">
                                            <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                            A la papelera
                                        </button>
                                    </MenuItem>
                                </div>
                            </template>
                            <template v-else>
                                <div class="px-1 py-1">
                                    <MenuItem v-slot="{ active }">
                                        <button @click="$emit('callRestoreItemFromArchive')" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
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
                                        <button @click="$emit('callForceDeleteItem')" :class="[
                                            active
                                                ? 'bg-sky-100 dark:bg-slate-500'
                                                : 'text-gray-900 dark:text-gray-400',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                        ]" title="Eliminar del todo">
                                            <TrashIcon :active="active" class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                            Eliminar
                                        </button>
                                    </MenuItem>
                                </div>
                            </template>
                        </template>
                    </template>
                </OptionsDropDown>
            </div>
            <!-- =========================================================== -->
        </div>
        <!-- <pre>{{ 'isTherePreviewData >> ' + isTherePreviewData }}</pre>
        <pre>{{ post }}</pre> -->
        <div class="mt-1">
            <ReadMoreOrLess :content="postBody" :showing-banner-if-content-is-null="false" :max-content-length="maxPostBodyLength"
                :content-classes="'ck-content-output'" />

            <UrlPreview :is-there-preview-data="isTherePreviewData" :url="post.preview.url" :preview="post.preview"
                :classes="'border border-cyan-200 rounded-lg bg-sky-50 w-1/2 mx-auto'" :img-classes="'w-full'" />
        </div>

        <div v-if="post.attachments.length > 0">
            <hr class="mx-0 mt-2" />

            <div class="grid gap-3 mt-2" :class="[
                post.attachments.length === 1
                    ? 'grid-cols-1'
                    : 'grid-cols-2 lg:grid-cols-3',
            ]">
                <PostAttachments :attachments="post.attachments" :max-preview-files="maxPreviewFiles"
                    @callOpenAttachmentsModal="openAttachmentPreview" />
            </div>

            <hr class="mx-0 mt-2" />
        </div>

        <hr v-else class="mx-0 mt-2" />

        <div class="px-2">
            <div v-if="post.total_of_reactions > 0 || post.total_of_comments > 0">
                <div class="flex items-center justify-between py-2 text-gray-500 dark:text-gray-300">
                    <div v-if="post.total_of_reactions > 0" class="flex items-center">
                        <div class="flex items-center -space-x-2">
                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'like' ||
                                (post.like_user_reactions &&
                                    post.like_user_reactions.length > 0)
                            " :title="'Me gusta'" :type="'like'" :z-index-icon="'z-[7]'"
                                :users-that-reacted="post.like_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['like'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'love' ||
                                (post.love_user_reactions &&
                                    post.love_user_reactions.length > 0)
                            " :title="'Me encanta'" :type="'love'" :z-index-icon="'z-[6]'"
                                :users-that-reacted="post.love_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['love'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'care' ||
                                (post.care_user_reactions &&
                                    post.care_user_reactions.length > 0)
                            " :title="'Me importa'" :type="'care'" :z-index-icon="'z-[5]'"
                                :users-that-reacted="post.care_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['care'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'haha' ||
                                (post.haha_user_reactions &&
                                    post.haha_user_reactions.length > 0)
                            " :title="'Me divierte'" :type="'haha'" :z-index-icon="'z-[4]'"
                                :users-that-reacted="post.haha_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['haha'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'wow' ||
                                (post.wow_user_reactions &&
                                    post.wow_user_reactions.length > 0)
                            " :title="'Me asombra'" :type="'wow'" :z-index-icon="'z-[3]'"
                                :users-that-reacted="post.wow_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['wow'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'sad' ||
                                (post.sad_user_reactions &&
                                    post.sad_user_reactions.length > 0)
                            " :title="'Me entristece'" :type="'sad'" :z-index-icon="'z-[2]'"
                                :users-that-reacted="post.sad_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['sad'])" />

                            <PostReactionTypeUsersSummary v-if="
                                post.current_user_type_reaction === 'angry' ||
                                (post.angry_user_reactions &&
                                    post.angry_user_reactions.length > 0)
                            " :title="'Me enoja'" :type="'angry'" :z-index-icon="'z-[1]'"
                                :users-that-reacted="post.angry_user_reactions" :current-user-type-reaction="post.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModal(post, defaultTabIndexObject['angry'])" />
                        </div>

                        <PostReactionTypeUsersSummary :users-that-reacted="post.all_user_reactions"
                            :current-user-has-reaction="post.current_user_has_reaction
                                " :total-of-reactions="post.total_of_reactions" :show-type-icon="false"
                            :show-header="false" @callOpenUserReactionsModal="openUserReactionsModal(post, 0)" />
                    </div>
                    <div v-else />

                    <PostCommentUsersSummary v-if="post.total_of_comments > 0"
                        :users-that-commented="post.all_users_that_commented"
                        :current-user-has-comment="post.current_user_has_comment"
                        :current-user-total-of-comments="post.current_user_total_of_comments"
                        :total-of-comments="post.total_of_comments" @callOpenDetailModal="openDetailModal" />
                </div>

                <hr />
            </div>

            <div v-if="!isTrashed && !isArchived" class="flex gap-2 my-1.5 font-bold text-gray-500 dark:text-gray-300">
                <PostReactionBox :post="post" @callRestartDefaultTabIndex="setTypeUserReactionsPostByType"
                    @callActiveShowNotification="activeShowNotification" />

                <div class="w-1/2">
                    <button title="Deja un comentario" @click="focusCommentTextArea"
                        class="flex items-center justify-center flex-1 w-full gap-1 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900">
                        <ChatBubbleLeftRightIcon class="w-6 h-6" />
                        Comentar
                    </button>
                </div>
            </div>

            <div>
                <hr v-if="!isTrashed && !isArchived">

                <PostCommentBox :is-trashed="isTrashed" :is-archived="isArchived" ref="postCommentBoxRef" :post="post"
                    :comments-list="commentsList" :type-list="typeList" @callOpenDetailModal="openDetailModal"
                    @callOpenAttachmentsModal="openCommentAttachmentPreview"
                    @callOpenUserReactionsModal="openUserReactionsModal"
                    @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostComments"
                    @callRestartPostCommentList="restartPostCommentList" @callConfirmDeletion="confirmDeletion"
                    @callActiveShowNotification="activeShowNotification" />
            </div>
        </div>
    </div>
</template>
