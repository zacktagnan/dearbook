<script setup>
import { ref, computed } from "vue";
import {
    ArrowDownTrayIcon,
    DocumentIcon,
    EllipsisVerticalIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';

const props = defineProps({
    post: Object,
});

const emit = defineEmits([
    "callOpenEditModal",
    "callOpenAttachmentsModal",
    "callActiveShowNotificationFromItem",
]);

const maxPostBodyLength = 100;

// const isImage = (attachment) => {
//     const mime = attachment.mime.split("/");
//     return mime[0].toLowerCase() === "image";
// };
import { isImage, isVideo } from "@/Libs/helpers";

// =======================================================================================

import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import PostHeader from "@/Components/dearbook/Post/Header.vue";
import { router, usePage } from "@inertiajs/vue3";

const openEditModal = () => {
    emit("callOpenEditModal", props.post);
};

const deletePost = () => {
    if (window.confirm("Are you sure you want to DELETE this post?")) {
        router.delete(route("post.destroy", props.post), {
            preserveScroll: true,
        });
    }
};

// =======================================================================================

const authUser = usePage().props.auth.user;

const isPostAuthor = computed(
    () => authUser && authUser.id === props.post.user.id
);

const maxPreviewFiles = 6;
const maxPreviewIndex = maxPreviewFiles - 1;

const openAttachmentPreview = (index) => {
    emit("callOpenAttachmentsModal", props.post, index);
};

import axiosClient from "@/axiosClient";
import { onMounted } from "vue";

onMounted(() => {
    allReactionsUsersPost();
    typeReactionsUsersPost("like");
    typeReactionsUsersPost("love");
    typeReactionsUsersPost("care");
    typeReactionsUsersPost("haha");
    typeReactionsUsersPost("wow");
    typeReactionsUsersPost("sad");
    typeReactionsUsersPost("angry");

    allCommentsUsersPost();
});

const allCommentsUsersPost = () => {
    axiosClient
        .get(route("post.all-comments-users", props.post))
        .then(({ data }) => {
            props.post.all_comments_users = data;
        });
};

const allReactionsUsersPost = () => {
    axiosClient
        .get(route("post.all-reactions-users", props.post))
        .then(({ data }) => {
            props.post.all_reactions_users = data;
        });
};

const typeReactionsUsersPost = (type) => {
    axiosClient
        .get(route("post.type-reactions-users", [props.post, type]))
        .then(({ data }) => {
            switch (type) {
                case "like":
                    props.post.like_reactions_users = data;
                    break;
                case "love":
                    props.post.love_reactions_users = data;
                    break;
                case "care":
                    props.post.care_reactions_users = data;
                    break;
                case "haha":
                    props.post.haha_reactions_users = data;
                    break;
                case "wow":
                    props.post.wow_reactions_users = data;
                    break;
                case "sad":
                    props.post.sad_reactions_users = data;
                    break;
                case "angry":
                    props.post.angry_reactions_users = data;
                    break;
                default:
                    break;
            }
        });
};

// ============================================================================

import PostReactionTypeUsersSummary from "@/Components/dearbook/Post/Reaction/TypeUsersSummary.vue";
import PostReactionBox from "@/Components/dearbook/Post/Reaction/Box.vue";

const activeShowNotification = (errors) => {
    emit("callActiveShowNotificationFromItem", errors);
};

import PostCommentUsersSummary from '@/Components/dearbook/Comment/UsersSummary.vue'
import PostCommentBox from '@/Components/dearbook/Comment/Box.vue'

const postCommentBoxRef = ref(null)
const focusCommentTextArea = () => {
    postCommentBoxRef.value.focusCommentTextAreaOfCreate()
};
</script>

<template>
    <div
        class="p-4 mt-4 mx-0.5 text- bg-white rounded shadow hover:shadow-cyan-900"
    >
        <div class="flex items-center justify-between">
            <PostHeader :post="post" />

            <!-- {{ authUser }}<br>
            {{ authUser.id }}<br>
            {{ post.user_id }}<br>
            {{ 'isPostAuthor' + isPostAuthor }} -->
            <!-- =========================================================== -->
            <Menu
                v-if="isPostAuthor"
                as="div"
                class="relative inline-block text-left"
            >
                <div>
                    <MenuButton
                        class="p-1 transition-colors duration-150 rounded-full hover:bg-black/5"
                        title="Ver opciones"
                    >
                        <EllipsisVerticalIcon
                            class="w-5 h-5"
                            aria-hidden="true"
                        />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="absolute right-0 z-10 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg w-28 ring-1 ring-black/5 focus:outline-none"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="openEditModal"
                                    :class="[
                                        active
                                            ? 'bg-indigo-100'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                    title="Edit"
                                >
                                    <PencilIcon
                                        :active="active"
                                        class="w-5 h-5 mr-2 text-indigo-400"
                                        aria-hidden="true"
                                    />
                                    Edit
                                </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="deletePost"
                                    :class="[
                                        active
                                            ? 'bg-indigo-100'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                    title="Eliminar"
                                >
                                    <TrashIcon
                                        :active="active"
                                        class="w-5 h-5 mr-2 text-indigo-400"
                                        aria-hidden="true"
                                    />
                                    Eliminar
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
            <!-- =========================================================== -->
        </div>

        <div class="mt-1">
            <ReadMoreOrLess :content="post.body" :max-content-length="maxPostBodyLength" />
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
                                    (post.like_reactions_users &&
                                        post.like_reactions_users.length > 0)
                                "
                                :title="'Me gusta'"
                                :type="'like'"
                                :z-index-icon="'z-[7]'"
                                :reaction-users="post.like_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'love' ||
                                    (post.love_reactions_users &&
                                        post.love_reactions_users.length > 0)
                                "
                                :title="'Me encanta'"
                                :type="'love'"
                                :z-index-icon="'z-[6]'"
                                :reaction-users="post.love_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'care' ||
                                    (post.care_reactions_users &&
                                        post.care_reactions_users.length > 0)
                                "
                                :title="'Me importa'"
                                :type="'care'"
                                :z-index-icon="'z-[5]'"
                                :reaction-users="post.care_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'haha' ||
                                    (post.haha_reactions_users &&
                                        post.haha_reactions_users.length > 0)
                                "
                                :title="'Me divierte'"
                                :type="'haha'"
                                :z-index-icon="'z-[4]'"
                                :reaction-users="post.haha_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'wow' ||
                                    (post.wow_reactions_users &&
                                        post.wow_reactions_users.length > 0)
                                "
                                :title="'Me asombra'"
                                :type="'wow'"
                                :z-index-icon="'z-[3]'"
                                :reaction-users="post.wow_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'sad' ||
                                    (post.sad_reactions_users &&
                                        post.sad_reactions_users.length > 0)
                                "
                                :title="'Me entristece'"
                                :type="'sad'"
                                :z-index-icon="'z-[2]'"
                                :reaction-users="post.sad_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />

                            <PostReactionTypeUsersSummary
                                v-if="
                                    post.current_user_type_reaction === 'angry' ||
                                    (post.angry_reactions_users &&
                                        post.angry_reactions_users.length > 0)
                                "
                                :title="'Me enoja'"
                                :type="'angry'"
                                :z-index-icon="'z-[1]'"
                                :reaction-users="post.angry_reactions_users"
                                :current-user-type-reaction="
                                    post.current_user_type_reaction
                                "
                            />
                        </div>

                        <PostReactionTypeUsersSummary
                            :reaction-users="post.all_reactions_users"
                            :current-user-has-reaction="
                                post.current_user_has_reaction
                            "
                            :total-of-reactions="post.total_of_reactions"
                            :show-type-icon="false"
                            :show-header="false"
                        />
                    </div>
                    <div v-else />

                    <PostCommentUsersSummary
                        v-if="post.total_of_comments > 0"
                        :comment-users="post.all_comments_users"
                        :current-user-has-comment="post.current_user_has_comment"
                        :current-user-total-of-comments="post.current_user_total_of_comments"
                        :total-of-comments="post.total_of_comments"
                    />
                </div>

                <hr />
            </div>

            <div class="flex gap-2 my-1.5 font-bold text-gray-500">
                <PostReactionBox
                    :post="post"
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

                <PostCommentBox ref="postCommentBoxRef" :post="post"
                    @callActiveShowNotificationToItem="activeShowNotification" />
            </div>
        </div>
    </div>
</template>
