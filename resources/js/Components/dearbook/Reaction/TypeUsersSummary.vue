<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

defineProps({
    reactionUsers: Object,
    title: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: '',
    },
    zIndexIcon: {
        type: Number,
        default: 0,
    },
    currentUserTypeReaction: {
        type: String,
        default: '',
    },
    currentUserHasReaction: {
        type: Boolean,
        default: false,
    },
    totalOfReactions: {
        type: Number,
        default: 0,
    },
    showTypeIcon: {
        type: Boolean,
        default: true,
    },
    showHeader: {
        type: Boolean,
        default: true,
    }
});

const authUser = usePage().props.auth.user

const showUsersPopover = ref(false)
</script>

<template>
    <div class="relative">
        <img v-if="showTypeIcon" :src="'/img/emojis/' + type + '.png'" :alt="title"
            class="relative w-[18px] h-[18px] mr-1.5 cursor-pointer ring-2 ring-white dark:ring-slate-900 rounded-full"
            :class="'z-[' + zIndexIcon + ']'" @mouseover="showUsersPopover = true"
            @mouseleave="showUsersPopover = false" />
        <span v-else class="cursor-pointer hover:underline" @mouseover="showUsersPopover = true"
            @mouseleave="showUsersPopover = false">
            {{ totalOfReactions }}
        </span>
        <div class="opacity-0 absolute z-20 p-2 text-[13px] leading-[15px] text-white rounded-lg bottom-6 bg-black/70 transition-all duration-500 whitespace-nowrap"
            :class="{
                'opacity-100': showUsersPopover
            }">
            <h3 v-if="showHeader" class="mb-1.5 text-[15px] font-bold">{{ title }}</h3>

            <p v-if="currentUserTypeReaction === type">
                {{ authUser.name }}
            </p>
            <p v-for="(userThatReact) of reactionUsers">
                {{ userThatReact.name }}
            </p>
        </div>
    </div>
</template>
