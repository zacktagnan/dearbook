<script setup>
import { UserMinusIcon, UserPlusIcon } from "@heroicons/vue/24/outline";
import { useForm } from "@inertiajs/vue3"

const props = defineProps({
    user: {
        type: Object,
    },
    isCurrentUserFollower: Boolean,
})

const followUnfollow = () => {
    const form = useForm({
        follow: !props.isCurrentUserFollower
    })

    form.post(route('user.follow-unfollow', props.user.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <button v-if="!isCurrentUserFollower" @click="followUnfollow"
        class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
        title="Seguir a este usuario">
        <UserPlusIcon class="w-5 h-5 md:mr-1" />
        <span class="hidden md:block">Seguir</span>
    </button>
    <button v-else @click="followUnfollow"
        class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-red-300 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-red-800 dark:hover:bg-red-100 focus:bg-red-800 dark:focus:bg-red-100 active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150"
        title="Dejar de seguir a este usuario">
        <UserMinusIcon class="w-5 h-5 md:mr-1" />
        <span class="hidden md:block">No Seguir</span>
    </button>
</template>
