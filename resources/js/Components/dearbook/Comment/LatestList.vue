<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToCommentBox'])

const activeShowNotificationToCommentBox = (errors) => {
    emit("callActiveShowNotificationToCommentBox", errors);
};
</script>

<template>
    <template v-if="post.latest_comments.length > 0">
        <!-- Listado
        {{ post.latest_comments }} -->
        <div v-for="latest_comment of post.latest_comments" class="flex gap-2 mt-3">
            <a :href="route('profile.index', { username: latest_comment.user.username })"
                :title="'Perfil de ' + latest_comment.user.name">
                <div class="w-8 avatar offline">
                    <img :src="latest_comment.user.avatar_url ||
                    '/img/default_avatar.png'" class="transition-all border-2 rounded-full hover:border-cyan-500"
                        :alt="latest_comment.user.name" />
                </div>
            </a>

            <CommentItem :comment="latest_comment" @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />
            <!-- <div class="flex flex-col w-full group/block_comment">
                <div class="flex items-center gap-1">
                    <div class="px-3 py-1 rounded-lg bg-gray-200/50">
                        <a :href="route('profile.index', { username: latest_comment.user.username })" class="text-[0.8125rem] font-semibold" :title="'Perfil de ' + latest_comment.user.name">
                            {{ latest_comment.user.name }}
                        </a>
                        <div class="text-sm text-justify whitespace-pre-line">{{ latest_comment.comment }}</div>
                    </div>

                    <Menu
                        v-if="isCommentAuthor(latest_comment)"
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
                    <div class="z-10 tooltip tooltip-right" :data-tip="latest_comment.created_at_large_format">
                        <small class="text-xs hover:cursor-pointer hover:underline">{{ latest_comment.created_at_formatted }}</small>
                    </div>

                    <CommentReactionBox :comment="latest_comment" @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />

                    <button class="font-extrabold hover:underline">Responder</button>

                    <div v-if="latest_comment.created_at != latest_comment.updated_at" class="tooltip tooltip-top" :data-tip="latest_comment.updated_at_large_format">
                        <small class="text-xs italic hover:cursor-pointer hover:underline">Editado</small>
                    </div>

                    {{ 'latest_comment.total_of_reactions: ' + latest_comment.total_of_reactions }}
                    <div v-if="latest_comment.total_of_reactions > 0" class="flex items-center">
                        <div v-if="true" class="flex items-center -space-x-0.5">
                            <CommentReactionTypeUsersSummary
                                v-if="
                                    latest_comment.current_user_type_reaction === 'like' ||
                                    (latest_comment.like_reactions_users &&
                                        latest_comment.like_reactions_users.length > 0)
                                "
                                :title="'Me gusta'"
                                :type="'like'"
                                :z-index-icon="'z-[7]'"
                                :reaction-users="latest_comment.like_reactions_users"
                                :current-user-type-reaction="
                                    latest_comment.current_user_type_reaction
                                "
                            />







                            <img src="/img/emojis/like.png" class="z-[7] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/love.png" class="z-[6] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/care.png" class="z-[5] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/haha.png" class="z-[4] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/wow.png" class="z-[3] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/sad.png" class="z-[2] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                            <img src="/img/emojis/angry.png" class="z-[1] w-4 h-4 rounded-full ring-1 ring-white" alt="">
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </template>
</template>
