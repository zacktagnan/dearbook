<script setup>
import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";

defineProps({
    commentUsers: Object,
    currentUserHasComment: {
        type: Boolean,
        default: false,
    },
    totalOfComments: {
        type: Number,
        default: 0,
    },
})

const authUser = usePage().props.auth.user;

const showUsersPopover = ref(false);

const maxUsersListed = 44;
const maxUsersIndex = maxUsersListed - 1;
</script>

<template>
    <div class="relative">
        <span
            v-if="totalOfComments > 0"
            class="cursor-pointer hover:underline text-[15px]"
            @mouseover="showUsersPopover = true"
            @mouseleave="showUsersPopover = false"
        >
            {{ totalOfComments }} {{ totalOfComments === 1 ? 'comentario' : 'comentarios' }}
        </span>

        <div
            class="opacity-0 absolute z-20 p-2 text-[13px] leading-[15px] text-white rounded-lg bottom-6 bg-black/70 transition-all duration-500 whitespace-nowrap"
            :class="{
                'opacity-100': showUsersPopover,
            }"
        >
            <template v-if="currentUserHasComment">
                <p v-if="currentUserHasComment">
                    {{ authUser.name }}
                </p>
            </template>

            <template v-if="commentUsers?.length > maxUsersListed">
                <template
                    v-for="(userThatComment, index) of commentUsers?.slice(
                        0,
                        maxUsersListed
                    )"
                >
                    <p
                        v-if="
                            index === maxUsersIndex &&
                            commentUsers.length > maxUsersListed
                        "
                    >
                        y {{ commentUsers.length - maxUsersIndex }} más...
                    </p>
                    <p v-else>
                        {{ userThatComment.name }}
                    </p>
                </template>
            </template>
            <template v-else>
                <p v-for="userThatComment of commentUsers">
                    {{ userThatComment.name }}
                </p>
            </template>
        </div>
    </div>
</template>
