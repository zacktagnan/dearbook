<script setup>
import { computed } from "vue";

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendCommentReaction'])

const mainTypeReactionFormat = computed(() => {
    if (!props.comment.current_user_has_reaction) {
        return {
            text: 'Like',
            classes: ''
        }
    } else {
        return {
            text: switchMainTypeReactionFormat[props.comment.current_user_type_reaction][0],
            classes: switchMainTypeReactionFormat[props.comment.current_user_type_reaction][1]
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
    <button @click="$emit('callSendCommentReaction', true, 'like')" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)" class="font-extrabold hover:underline" :class="mainTypeReactionFormat.classes"
    >
        {{ mainTypeReactionFormat.text }}
    </button>
</template>
