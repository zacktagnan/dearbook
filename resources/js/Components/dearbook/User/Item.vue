<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    user: Object,
    classes: {
        type: String,
        default: '',
    },
    userSinceDate: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div :class="'rounded-md transition-all' + classes">
        <div :href="user.username ? route('profile.index', { username: user.username }) : '#'" class="flex items-center gap-2 px-2 py-1">
            <Link v-if="user.username" :href="route('profile.index', { username: user.username })">
            <img :src="user.avatar_url" class="w-[36px] rounded-full bg-gray-200 dark:bg-gray-400" :alt="user.name">
            </Link>
            <img v-else :src="user.avatar_url" class="w-[36px] rounded-full bg-gray-200 dark:bg-gray-400" :alt="user.name">
            <div class="flex justify-between items-center flex-1">
                <div class="flex-col leading-none">
                    <Link v-if="user.username" :href="route('profile.index', { username: user.username })">
                    <h3 class="font-black hover:underline">{{ user.name }}</h3>
                    </Link>
                    <h3 v-else class="font-black hover:underline">{{ user.name }}</h3>
                    <span v-if="userSinceDate" class="text-xs text-gray-500 dark:text-gray-300">{{ userSinceDate
                        }}</span>
                </div>

                <div class="flex gap-1 md:gap-2">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
