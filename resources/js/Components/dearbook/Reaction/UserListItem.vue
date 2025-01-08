<script setup>
import FollowGeneralButton from "@/Components/FollowGeneralButton.vue";
// import { UserPlusIcon, } from "@heroicons/vue/24/solid";
import { usePage } from "@inertiajs/vue3"
import { computed } from "vue"

const props = defineProps({
    user: Object,
    type: String,
    title: String,
})

const authUser = usePage().props.auth.user

const isNotAuthUser = computed(
    () => !!(props.user.id !== authUser.id)
)

const updateFollowStatus = (isFollowedBy) => {
    props.user.is_followed_by = isFollowedBy
}
</script>

<template>
    <div class="flex justify-between mt-4 font-bold">
        <!-- {{ $page.props.auth.user.name }} || {{ entity.current_user_type_reaction }} -->
        <div class="flex items-center gap-3">
            <a :href="route('profile.index', { username: user.username })" :title="'Perfil de ' + user.name"
                class="relative">
                <img :src="user.avatar_url" class="w-10 transition-all border-2 rounded-full hover:border-cyan-500"
                    :alt="user.name" />

                <img :src="'/img/emojis/' + type + '.png'" :alt="title"
                    class="absolute w-4 h-4 left-[26px] top-[26px]" />
            </a>

            <a :href="route('profile.index', { username: user.username })" :title="'Perfil de ' + user.name">
                {{ user.name }}
            </a>
        </div>

        <!-- <button v-if="isNotAuthUser"
            class="flex items-center gap-1 px-2 my-0.5 bg-gray-200 dark:bg-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-150">
            <UserPlusIcon class="w-5 h-5" />
            Agregar a amigos
        </button> -->
        <FollowGeneralButton v-if="isNotAuthUser" :user="user" :is-current-user-follower="user.is_followed_by"
            @call-follow-status-change="updateFollowStatus" />
        <!-- <FollowGeneralButton v-if="isNotAuthUser" :user="user" :is-current-user-follower="user.is_followed_by" /> -->
    </div>
</template>
