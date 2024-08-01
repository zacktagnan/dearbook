<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    usersThatReacted: Object,
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

const emit = defineEmits([
    "callOpenUserReactionsModal",
]);

const authUser = usePage().props.auth.user;

const showUsersPopover = ref(false);

const maxUsersListed = 4;
const maxUsersIndex = maxUsersListed - 1;

const usersThatReactedLength = computed(() => {
    let total = 0
    if (props.currentUserTypeReaction !== '' && props.currentUserTypeReaction === props.type) {
        total++
    }
    if (props.usersThatReacted?.length > 0) {
        total += props.usersThatReacted?.length
    }

    return total
})
</script>

<template>
    <div class="relative">
        <img v-if="showTypeIcon" @click="$emit('callOpenUserReactionsModal')" :src="'/img/emojis/' + type + '.png'"
            :alt="title" class="relative w-4 h-4 rounded-full cursor-pointer ring-1 ring-white dark:ring-slate-900"
            :class="zIndexIcon" @mouseover="showUsersPopover = true" @mouseleave="showUsersPopover = false" />
        <span v-else @click="$emit('callOpenUserReactionsModal')" class="mr-0.5 cursor-pointer hover:underline"
            @mouseover="showUsersPopover = true" @mouseleave="showUsersPopover = false">
            {{ totalOfReactions }}
        </span>
        <div :class="[
            'absolute z-20 p-2 text-[13px] leading-[15px] text-white rounded-lg -left-12 bottom-6 bg-black/70 transition-all duration-500 whitespace-nowrap',
            showUsersPopover
                ? 'opacity-100 visible'
                : 'opacity-0 invisible'
        ]">
            <h3 v-if="showHeader" class="mb-1.5 text-sm items-center flex gap-1">
                <img :src="'/img/emojis/' + type + '.png'" :alt="title" class="w-4 h-4" />

                <span>{{ usersThatReactedLength }}</span>
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

            <template v-if="usersThatReacted?.length > maxUsersListed">
                <template v-for="(userThatReact, index) of usersThatReacted?.slice(
                    0,
                    maxUsersListed
                )">
                    <p v-if="
                        index === maxUsersIndex &&
                        usersThatReacted.length > maxUsersListed
                    ">
                        y {{ usersThatReacted.length - maxUsersIndex }} más...
                    </p>
                    <p v-else>
                        {{ userThatReact.user?.name }}
                    </p>
                </template>
            </template>
            <template v-else>
                <p v-for="userThatReact of usersThatReacted">
                    {{ userThatReact.user?.name }}
                </p>
            </template>
        </div>
    </div>
</template>
