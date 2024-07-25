<script setup>
import { ref, computed } from "vue";
import {
    ArrowDownTrayIcon,
    DocumentIcon,
} from "@heroicons/vue/24/solid";
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';

const props = defineProps({
    post: Object,
    typeList: {
        type: String,
        default: 'latest',
    },
});

const emit = defineEmits([
    "callOpenEditModal",
    "callOpenDetailModal",
    "callOpenAttachmentsModal",
    'callOpenUserReactionsModal',
    'callConfirmDeletion',
    "callActiveShowNotificationFromItem",
]);

const maxPostBodyLength = 100;

import { isImage, isVideo, reactionTypesFormat, } from "@/Libs/helpers";

// =======================================================================================

import PostHeader from "@/Components/dearbook/Post/Header.vue";
import EditDeleteDropdown from "@/Components/dearbook/EditDeleteDropdown.vue";
import { usePage } from "@inertiajs/vue3";

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

const maxPreviewFiles = 6;
const maxPreviewIndex = maxPreviewFiles - 1;

const openAttachmentPreview = (index) => {
    emit("callOpenAttachmentsModal", props.post, index, 'post');
};

const openCommentAttachmentPreview = (comment, index, entityPrefix) => {
    emit("callOpenAttachmentsModal", comment, index, entityPrefix);
}

import { onMounted } from "vue";

onMounted(() => {
    setTypeUserReactionsPostByType()
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

import PostReactionTypeUsersSummary from "@/Components/dearbook/Post/Reaction/TypeUsersSummary.vue";
import PostReactionBox from "@/Components/dearbook/Post/Reaction/Box.vue";

const openUserReactionsModal = (entity, tabIndex) => {
    emit("callOpenUserReactionsModal", entity, tabIndex);
};

const confirmDeletion = (entity, entityPrefix) => {
    emit("callConfirmDeletion", entity, entityPrefix);
}

const activeShowNotification = (errors) => {
    emit("callActiveShowNotificationFromItem", errors);
};

import PostCommentUsersSummary from '@/Components/dearbook/Comment/UsersSummary.vue'
import PostCommentBox from '@/Components/dearbook/Comment/Box.vue'

const postCommentBoxRef = ref(null)
const focusCommentTextArea = () => {
    postCommentBoxRef.value.focusCommentTextAreaOfCreate()
};

const filterDeletedComment = (commentId) => {
    props.post.all_comments = props.post.all_comments.filter(c => c.id != commentId)
    props.post.total_of_comments--
}

defineExpose({
    filterDeletedComment,
})
</script>

<template>
    <div
        class="p-4 mx-0.5 bg-white"
        :class="[
            typeList === 'latest'
                ? 'mt-4 rounded shadow hover:shadow-cyan-900'
                : ''
        ]"
    >
        <div class="flex items-center justify-between">
            <PostHeader :post="post" />

            <EditDeleteDropdown v-model="isPostAuthor"
                @callEditItem="openEditModal" @callDeleteItem="$emit('callConfirmDeletion', post, 'post')"
                :ellipsis-type-icon="'vertical'" :menu-items-classes="'w-28'" />
            <!-- =========================================================== -->
        </div>

        <!-- <pre>{{ post }}</pre> -->

        <div class="mt-1">
            <ReadMoreOrLess :content="post.body" :max-content-length="maxPostBodyLength"
                :content-classes="'ck-content-output'" />
        </div>

        <div v-if="post.attachments.length > 0">
            <hr class="mx-0 mt-2" />

            <div
                class="grid gap-3 mt-2"
                :class="[
                    post.attachments.length === 1
                        ? 'grid-cols-1'
                        : 'grid-cols-2 lg:grid-cols-3',
                ]"
            >
                <template
                    v-for="(attachment, index) of post.attachments.slice(
                        0,
                        maxPreviewFiles
                    )"
                >
                    <div
                        @click="openAttachmentPreview(index)"
                        title="Ver en detalle"
                        class="relative flex flex-col items-center justify-center text-gray-500 cursor-pointer aspect-square bg-cyan-100 group hover:bg-sky-700/40"
                    >
                        <div
                            v-if="
                                index === maxPreviewIndex &&
                                post.attachments.length > maxPreviewFiles
                            "
                            class="absolute inset-0 flex items-center justify-center text-[24px] md:text-[28px] text-white bg-black/60"
                        >
                            +{{ post.attachments.length - maxPreviewIndex }}
                        </div>

                        <a
                            :href="
                                route('post.download-attachment', attachment)
                            "
                            v-if="index < maxPreviewIndex"
                            title="Descargar"
                            class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-2 top-2"
                        >
                            <ArrowDownTrayIcon class="w-5 h-5" />
                        </a>

                        <template
                            v-if="isImage(attachment) || isVideo(attachment)"
                        >
                            <img
                                v-if="isImage(attachment)"
                                :src="attachment.url"
                                :alt="attachment.name"
                                class="object-contain w-10/12 aspect-square"
                            />
                            <video
                                v-if="isVideo(attachment)"
                                :src="attachment.url"
                                controls
                                :alt="attachment.name"
                                class="object-contain w-10/12 aspect-square"
                            ></video>
                        </template>

                        <template v-else>
                            <div
                                class="flex flex-col items-center justify-center"
                            >
                                <DocumentIcon
                                    class="w-12 h-12 lg:w-16 lg:h-16"
                                />

                                <span class="text-sm lg:text-base">
                                    {{ attachment.name }}
                                </span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <hr class="mx-0 mt-2" />
        </div>

        <hr v-else class="mx-0 mt-2" />

        <div class="px-2">
            <div v-if="post.total_of_reactions > 0 || post.total_of_comments > 0">
                <div class="flex items-center justify-between py-2 text-gray-500">
                    <div v-if="post.total_of_reactions > 0" class="flex items-center">
                        <div class="flex items-center -space-x-2">
                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'like' ||
                                    (post.like_user_reactions &&
                                        post.like_user_reactions.length > 0)
                                "
                                :title="'Me gusta'"
                                :type="'like'"
                                :z-index-icon="'z-[7]'"
                                :users-that-reacted="post.like_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['like'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'love' ||
                                    (post.love_user_reactions &&
                                        post.love_user_reactions.length > 0)
                                "
                                :title="'Me encanta'"
                                :type="'love'"
                                :z-index-icon="'z-[6]'"
                                :users-that-reacted="post.love_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['love'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'care' ||
                                    (post.care_user_reactions &&
                                        post.care_user_reactions.length > 0)
                                "
                                :title="'Me importa'"
                                :type="'care'"
                                :z-index-icon="'z-[5]'"
                                :users-that-reacted="post.care_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['care'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'haha' ||
                                    (post.haha_user_reactions &&
                                        post.haha_user_reactions.length > 0)
                                "
                                :title="'Me divierte'"
                                :type="'haha'"
                                :z-index-icon="'z-[4]'"
                                :users-that-reacted="post.haha_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['haha'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'wow' ||
                                    (post.wow_user_reactions &&
                                        post.wow_user_reactions.length > 0)
                                "
                                :title="'Me asombra'"
                                :type="'wow'"
                                :z-index-icon="'z-[3]'"
                                :users-that-reacted="post.wow_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['wow'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'sad' ||
                                    (post.sad_user_reactions &&
                                        post.sad_user_reactions.length > 0)
                                "
                                :title="'Me entristece'"
                                :type="'sad'"
                                :z-index-icon="'z-[2]'"
                                :users-that-reacted="post.sad_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['sad'])"
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'angry' ||
                                    (post.angry_user_reactions &&
                                        post.angry_user_reactions.length > 0)
                                "
                                :title="'Me enoja'"
                                :type="'angry'"
                                :z-index-icon="'z-[1]'"
                                :users-that-reacted="post.angry_user_reactions"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                                @callOpenUserReactionsModalToItem="openUserReactionsModal(post, defaultTabIndexObject['angry'])"
                            />
                        </div>

                        <PostReactionTypeUsersSummary
                            :users-that-reacted="post.all_user_reactions"
                            :current-user-has-reaction="
                                post.current_user_has_reaction
                            "
                            :total-of-reactions="post.total_of_reactions"
                            :show-type-icon="false"
                            :show-header="false"
                            @callOpenUserReactionsModalToItem="openUserReactionsModal(post, 0)"
                        />
                    </div>
                    <div v-else />

                    <PostCommentUsersSummary
                        v-if="post.total_of_comments > 0"
                        :users-that-commented="post.all_users_that_commented"
                        :current-user-has-comment="post.current_user_has_comment"
                        :current-user-total-of-comments="post.current_user_total_of_comments"
                        :total-of-comments="post.total_of_comments"
                        @callOpenDetailModalToItem="openDetailModal"
                    />
                </div>

                <hr />
            </div>

            <div class="flex gap-2 my-1.5 font-bold text-gray-500">
                <PostReactionBox
                    :post="post"
                    @callRestartDefaultTabIndex="setTypeUserReactionsPostByType"
                    @callActiveShowNotificationToItem="activeShowNotification"
                />

                <div class="w-1/2">
                    <button
                        title="Deja un comentario"
                        @click="focusCommentTextArea"
                        class="flex items-center justify-center flex-1 w-full gap-1 px-4 py-2 rounded-lg hover:bg-gray-100"
                    >
                        <ChatBubbleLeftRightIcon class="w-6 h-6" />
                        Comentar
                    </button>
                </div>
            </div>

            <div>
                <hr>

                <PostCommentBox ref="postCommentBoxRef" :post="post" :type-list="typeList"
                    @callOpenDetailModalToItem="openDetailModal"
                    @callOpenAttachmentsModalToItem="openCommentAttachmentPreview"
                    @callOpenUserReactionsModalToItem="openUserReactionsModal"
                    @callConfirmDeletionToItem="confirmDeletion"
                    @callActiveShowNotificationToItem="activeShowNotification" />
            </div>
        </div>
    </div>
</template>
