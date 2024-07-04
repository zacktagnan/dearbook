<script setup>
import { UserPlusIcon, } from "@heroicons/vue/24/solid";

defineProps({
    user: Object,
    type: String,
})
</script>

<template>
    <div class="flex justify-between mt-4 font-bold">
        <!-- {{ $page.props.auth.user.name }} || {{ entity.current_user_type_reaction }} -->
        <div class="flex items-center gap-3">
            <a :href="route('profile.index', { username: user.username })"
                :title="'Perfil de ' + user.name" class="relative">
                <img :src="user.avatar_url ||
                '/img/default_avatar.png'" class="w-10 transition-all border-2 rounded-full hover:border-cyan-500"
                    :alt="user.name" />

                <img
                :src="'/img/emojis/' + type + '.png'"
                :alt="text"
                class="absolute w-4 h-4 left-[26px] top-[26px]" />
            </a>

            <a :href="route('profile.index', { username: user.username })"
                :title="'Perfil de ' + user.name">
                {{ user.name }}
            </a>
        </div>

        <button class="flex items-center gap-1 px-2 my-0.5 bg-gray-200 rounded-md hover:bg-gray-300">
            <UserPlusIcon class="w-5 h-5" />
            Agregar a amigos
        </button>
    </div>
</template>
