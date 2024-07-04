<script setup>
import { computed } from "vue";
import { reactionTypesFormat } from "@/Libs/helpers";

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendCommentReaction'])

const mainTypeReactionFormat = computed(() => {
    if (!props.comment.current_user_has_reaction) {
        return {
            text: reactionTypesFormat.all.main_type_button_text,
            classes: reactionTypesFormat.all.classes,
        }
    } else {
        return {
            text: reactionTypesFormat[props.comment.current_user_type_reaction].main_type_button_text,
            classes: reactionTypesFormat[props.comment.current_user_type_reaction].classes,
        }
    }
})
</script>

<template>
    <button @click="$emit('callSendCommentReaction', true, 'like')" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)" class="font-extrabold hover:underline" :class="mainTypeReactionFormat.classes"
    >
        {{ mainTypeReactionFormat.text }}
    </button>
</template>
