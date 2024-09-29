<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    user: Object,
    classes: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div :class="'rounded-md cursor-pointer transition-all' + classes">
        <Link :href="route('profile.index', { username: user.username })" class="flex items-center gap-2 px-2 py-1">
        <img :src="user.avatar_url ||
            '/img/default_avatar.png'" class="mt-1 w-[36px] rounded-full" :alt="title">
        <div class="flex justify-between items-center flex-1">
            <h3 class="text-lg font-black">{{ user.name }}</h3>

            <div class="flex gap-1 md:gap-2">
                <slot />
            </div>
        </div>
        </Link>
    </div>
</template>
