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
        <div :href="route('profile.index', { username: user.username })" class="flex items-center gap-2 px-2 py-1">
            <Link :href="route('profile.index', { username: user.username })">
            <img :src="user.avatar_url" class="w-[36px] rounded-full" :alt="user.name">
            </Link>
            <div class="flex justify-between items-center flex-1">
                <div class="flex-col leading-none">
                    <Link :href="route('profile.index', { username: user.username })">
                    <h3 class="font-black hover:underline">{{ user.name }}</h3>
                    </Link>
                    <span v-if="userSinceDate" class="text-xs text-gray-500 dark:text-gray-300">{{ userSinceDate }}</span>
                </div>

                <div class="flex gap-1 md:gap-2">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
