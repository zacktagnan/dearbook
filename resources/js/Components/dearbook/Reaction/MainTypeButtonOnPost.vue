<script setup>
import { HandThumbUpIcon, } from "@heroicons/vue/24/outline";
import { computed } from "vue";

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendPostReaction'])

const mainTypeReactionFormat = computed(() => {
    if (!props.post.current_user_has_reaction) {
        return {
            text: 'Like',
            classes: ''
        }
    } else {
        return {
            text: switchMainTypeReactionFormat[props.post.current_user_type_reaction][0],
            classes: switchMainTypeReactionFormat[props.post.current_user_type_reaction][1]
        }
    }
})

const switchMainTypeReactionFormat = {
    'like': ['Like', 'text-sky-500'],
    'love': ['Love', 'text-rose-500'],
    'care': ['Care', 'text-yellow-500'],
    'haha': ['Haha', 'text-yellow-500'],
    'wow': ['Wow', 'text-yellow-500'],
    'sad': ['Sad', 'text-blue-300'],
    'angry': ['Angry', 'text-orange-600'],
};
</script>

<template>
    <button @click="$emit('callSendPostReaction', true, 'like')" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)"
        class="flex items-center justify-center flex-1 w-full gap-1 px-4 py-2 rounded-lg hover:bg-gray-100"
        :class="mainTypeReactionFormat.classes">
        <HandThumbUpIcon v-if="!post.current_user_has_reaction" class="w-6 h-6" />
        <img v-else-if="post.current_user_type_reaction == 'like'" src="/img/emojis/like.png" alt="Like" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'love'" src="/img/emojis/love.png" alt="Love" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'care'" src="/img/emojis/care.png" alt="Care" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'haha'" src="/img/emojis/haha.png" alt="Haha" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'wow'" src="/img/emojis/wow.png" alt="Wow" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'sad'" src="/img/emojis/sad.png" alt="Sad" class="w-6" />
        <img v-else-if="post.current_user_type_reaction == 'angry'" src="/img/emojis/angry.png" alt="Angry"
            class="w-6" />
        {{ mainTypeReactionFormat.text }}
    </button>
</template>
