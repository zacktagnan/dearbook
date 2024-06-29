<script setup>
import { EllipsisHorizontalIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import CommentReactionBox from '@/Components/dearbook/Comment/Reaction/Box.vue'
import CommentReactionTypeUsersSummary from "@/Components/dearbook/Comment/Reaction/TypeUsersSummary.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import axiosClient from "@/axiosClient";

const props = defineProps({
    comment: Object,
});
onMounted(() => {
    allReactionsUsersComment();
    typeReactionsUsersComment("like");
    typeReactionsUsersComment("love");
    typeReactionsUsersComment("care");
    typeReactionsUsersComment("haha");
    typeReactionsUsersComment("wow");
    typeReactionsUsersComment("sad");
    typeReactionsUsersComment("angry");
});

const authUser = usePage().props.auth.user;

const isCommentAuthor = () => authUser && authUser.id === props.comment.user.id

const allReactionsUsersComment = () => {
    axiosClient
        .get(route("comment.all-reactions-users", props.comment.id))
        .then(({ data }) => {
            props.comment.all_reactions_users = data;
        });
};

const typeReactionsUsersComment = (type) => {
    axiosClient
        .get(route("comment.type-reactions-users", [props.comment.id, type]))
        .then(({ data }) => {
            switch (type) {
                case "like":
                    props.comment.like_reactions_users = data;
                    break;
                case "love":
                    props.comment.love_reactions_users = data;
                    break;
                case "care":
                    props.comment.care_reactions_users = data;
                    break;
                case "haha":
                    props.comment.haha_reactions_users = data;
                    break;
                case "wow":
                    props.comment.wow_reactions_users = data;
                    break;
                case "sad":
                    props.comment.sad_reactions_users = data;
                    break;
                case "angry":
                    props.comment.angry_reactions_users = data;
                    break;
                default:
                    break;
            }
        });
};

const emit = defineEmits(['callActiveShowNotificationToLatestList'])

const activeShowNotificationToLatestList = (errors) => {
    emit("callActiveShowNotificationToLatestList", errors);
};
</script>

<template>
    <div class="flex flex-col w-full group/block_comment">
        <div class="flex items-center gap-1">
            <div class="px-3 py-1 rounded-lg bg-gray-200/50">
                <a :href="route('profile.index', { username: comment.user.username })" class="text-[0.8125rem] font-semibold" :title="'Perfil de ' + comment.user.name">
                    {{ comment.user.name }}
                </a>
                <div class="text-sm text-justify whitespace-pre-line">{{ comment.comment }}</div>
            </div>

            <Menu
                v-if="isCommentAuthor()"
                as="div"
                class="relative inline-block text-left"
            >
                <div>
                    <MenuButton
                        class="p-1 transition-colors duration-150 rounded-full opacity-0 hover:bg-black/5 group-hover/block_comment:opacity-100"
                        title="Ver opciones"
                    >
                        <EllipsisHorizontalIcon
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
                        class="absolute right-0 z-10 w-24 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black/5 focus:outline-none"
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
                                    Edit
                                </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="deleteComment"
                                    :class="[
                                        active
                                            ? 'bg-indigo-100'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                    title="Eliminar"
                                >
                                    Eliminar
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>

        <div class="flex items-center gap-4 px-3 mt-0.5 text-xs text-gray-600">
            <div class="z-10 tooltip tooltip-right" :data-tip="comment.created_at_large_format">
                <small class="text-xs hover:cursor-pointer hover:underline">{{ comment.created_at_formatted }}</small>
            </div>

            <CommentReactionBox :comment="comment" @callActiveShowNotificationToCommentItem="activeShowNotificationToLatestList" />

            <button class="font-extrabold hover:underline">Responder</button>

            <div v-if="comment.created_at != comment.updated_at" class="tooltip tooltip-top" :data-tip="comment.updated_at_large_format">
                <small class="text-xs italic hover:cursor-pointer hover:underline">Editado</small>
            </div>

            <!-- {{ 'comment.total_of_reactions: ' + comment.total_of_reactions }} -->
            <div v-if="comment.total_of_reactions > 0" class="flex items-center">
                <CommentReactionTypeUsersSummary
                    :reaction-users="comment.all_reactions_users"
                    :current-user-has-reaction="
                        comment.current_user_has_reaction
                    "
                    :total-of-reactions="comment.total_of_reactions"
                    :show-type-icon="false"
                    :show-header="false"
                />

                <div class="flex items-center -space-x-0.5">
                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'like' ||
                            (comment.like_reactions_users &&
                                comment.like_reactions_users.length > 0)
                        "
                        :title="'Me gusta'"
                        :type="'like'"
                        :z-index-icon="'z-[7]'"
                        :reaction-users="comment.like_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'love' ||
                            (comment.love_reactions_users &&
                                comment.love_reactions_users.length > 0)
                        "
                        :title="'Me encanta'"
                        :type="'love'"
                        :z-index-icon="'z-[6]'"
                        :reaction-users="comment.love_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'care' ||
                            (comment.care_reactions_users &&
                                comment.care_reactions_users.length > 0)
                        "
                        :title="'Me importa'"
                        :type="'care'"
                        :z-index-icon="'z-[5]'"
                        :reaction-users="comment.care_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'haha' ||
                            (comment.haha_reactions_users &&
                                comment.haha_reactions_users.length > 0)
                        "
                        :title="'Me divierte'"
                        :type="'haha'"
                        :z-index-icon="'z-[4]'"
                        :reaction-users="comment.haha_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'wow' ||
                            (comment.wow_reactions_users &&
                                comment.wow_reactions_users.length > 0)
                        "
                        :title="'Me asombra'"
                        :type="'wow'"
                        :z-index-icon="'z-[3]'"
                        :reaction-users="comment.wow_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'sad' ||
                            (comment.sad_reactions_users &&
                                comment.sad_reactions_users.length > 0)
                        "
                        :title="'Me entristece'"
                        :type="'sad'"
                        :z-index-icon="'z-[2]'"
                        :reaction-users="comment.sad_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'angry' ||
                            (comment.angry_reactions_users &&
                                comment.angry_reactions_users.length > 0)
                        "
                        :title="'Me enoja'"
                        :type="'angry'"
                        :z-index-icon="'z-[1]'"
                        :reaction-users="comment.angry_reactions_users"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                    />
                </div>
            </div>
        </div>
    </div>
</template>
