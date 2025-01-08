<script setup>
import { UserMinusIcon, UserPlusIcon } from "@heroicons/vue/24/outline";
import { useForm } from "@inertiajs/vue3"
import { computed, onMounted, ref, watch } from "vue"
import { followState } from "@/StoresGlobal/followState";

const props = defineProps({
    user: {
        type: Object,
    },
    isCurrentUserFollower: Boolean,
})

const emit = defineEmits(['callFollowStatusChange'])

const isAuthUserFollower = ref(props.isCurrentUserFollower)

// const localFollowStatus = ref(props.isCurrentUserFollower)
// const isAuthUserFollower = computed({
//     get: () => followState.getFollowStatus(props.user.id) || localFollowStatus.value,
//     set: (value) => followState.setFollowStatus(props.user.id, value)
// })

// onMounted(() => {
//     if (!followState.followStatus.hasOwnProperty(props.user.id)) {
//         followState.setFollowStatus(props.user.id, localFollowStatus.value)
//     }
// })

// Observar el estado global y forzar la actualización del componente
// watch(() => followState.getFollowStatus(props.user.id), (newVal) => {
//     isAuthUserFollower.value = newVal
// })

const followUnfollow = () => {
    const form = useForm({
        // follow: !props.isCurrentUserFollower
        follow: !isAuthUserFollower.value
    })

    form.post(route('user.follow-unfollow', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            isAuthUserFollower.value = !isAuthUserFollower.value
            emit('callFollowStatusChange', isAuthUserFollower.value)
            // ---------------
            // followState.setFollowStatus(props.user.id, !isAuthUserFollower.value)
            // ---------------
            // followState.setFollowStatus(props.user.id, !isAuthUserFollower.value)
            // isAuthUserFollower.value = !isAuthUserFollower.value
            // localFollowStatus.value = !localFollowStatus.value
        }
    })
}
</script>

<template>
    <!-- <button v-if="!isAuthUserFollower" @click="followUnfollow"
        class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
        title="Seguir a este usuario">
        <UserPlusIcon class="w-5 h-5 md:mr-1" />
        <span class="hidden md:block">Agregar a amigos</span>
    </button>
    <button v-else @click="followUnfollow"
        class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-red-300 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-red-800 dark:hover:bg-red-100 focus:bg-red-800 dark:focus:bg-red-100 active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150"
        title="Dejar de seguir a este usuario">
        <UserMinusIcon class="w-5 h-5 md:mr-1" />
        <span class="hidden md:block">Dejar la amistad</span>
    </button> -->

    <button v-if="!isAuthUserFollower" @click="followUnfollow"
        class="flex items-center gap-1 px-2 my-0.5 bg-gray-200 dark:bg-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-150">
        <UserPlusIcon class="w-5 h-5" />
        Agregar a amigos
    </button>
    <button v-else @click="followUnfollow"
        class="flex items-center gap-1 px-2 my-0.5 bg-red-100 dark:bg-red-300 hover:bg-red-200 dark:hover:bg-red-400 rounded-md transition-colors duration-150">
        <UserMinusIcon class="w-5 h-5" />
        Dejar la amistad
    </button>
</template>
