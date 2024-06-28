<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

defineProps({
    reactionUsers: Object,
    title: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "",
    },
    zIndexIcon: {
        type: String,
        default: "0",
    },
    currentUserTypeReaction: {
        type: String,
        default: "",
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
    },
});

const authUser = usePage().props.auth.user;

const showUsersPopover = ref(false);

const maxUsersListed = 4;
const maxUsersIndex = maxUsersListed - 1;
</script>

<template>
    <div class="relative">
        <img
            v-if="showTypeIcon"
            :src="'/img/emojis/' + type + '.png'"
            :alt="title"
            class="relative w-[18px] h-[18px] mr-1.5 cursor-pointer ring-2 ring-white dark:ring-slate-900 rounded-full"
            :class="zIndexIcon"
            @mouseover="showUsersPopover = true"
            @mouseleave="showUsersPopover = false"
        />
        <span
            v-else
            class="cursor-pointer hover:underline text-[15px]"
            @mouseover="showUsersPopover = true"
            @mouseleave="showUsersPopover = false"
        >
            {{ totalOfReactions }}
        </span>
        <div
            class="opacity-0 absolute z-20 p-2 text-[13px] leading-[15px] text-white rounded-lg bottom-6 bg-black/70 transition-all duration-500 whitespace-nowrap"
            :class="{
                'opacity-100': showUsersPopover,
            }"
        >
            <h3 v-if="showHeader" class="mb-1.5 text-[15px] font-bold">
                {{ title }}
            </h3>

            <template v-if="currentUserHasReaction">
                <p v-if="currentUserHasReaction">
                    {{ authUser.name }}
                </p>
            </template>
            <template v-if="currentUserTypeReaction !== ''">
                <p v-if="currentUserTypeReaction === type">
                    {{ authUser.name }}
                </p>
            </template>

            <template v-if="reactionUsers?.length > maxUsersListed">
                <template
                    v-for="(userThatReact, index) of reactionUsers?.slice(
                        0,
                        maxUsersListed
                    )"
                >
                    <p
                        v-if="
                            index === maxUsersIndex &&
                            reactionUsers.length > maxUsersListed
                        "
                    >
                        y {{ reactionUsers.length - maxUsersIndex }} más...
                    </p>
                    <p v-else>
                        {{ userThatReact.name }}
                    </p>
                </template>
            </template>
            <template v-else>
                <p v-for="userThatReact of reactionUsers">
                    {{ userThatReact.name }}
                </p>
            </template>
        </div>
    </div>
</template>
