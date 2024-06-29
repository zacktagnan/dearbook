<script setup>
import ReactionTypeBar from '@/Components/dearbook/Comment/Reaction/TypeBar.vue'
import ReactionMainTypeButton from '@/Components/dearbook/Comment/Reaction/MainTypeButton.vue'
import { ref } from "vue";

import axiosClient from '@/axiosClient'

const showReactionTypeBar = ref(false)

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['callActiveShowNotificationToCommentItem'])

const changeShowReactionTypeBar = (value) => {
    showReactionTypeBar.value = value
}

const sendCommentReaction = (from, type) => {
    axiosClient.post(route('comment.reaction', props.comment.id), {
        from_main_reaction_button: from,
        reaction_type: type,
        current_reaction_type: props.comment.current_user_type_reaction,
    })
        .then(({ data }) => {
            props.comment.current_user_type_reaction = data.current_user_type_reaction
            props.comment.current_user_has_reaction = data.current_user_has_reaction
            props.comment.total_of_reactions = data.total_of_reactions
        })
        .catch((error) => {
            emit('callActiveShowNotificationToCommentItem', error.response.data.errors)
        })
    changeShowReactionTypeBar(false)
}
</script>

<template>
    <div class="relative">
        <ReactionTypeBar :showReactionTypeBar="showReactionTypeBar"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendCommentReaction="sendCommentReaction" />

        <ReactionMainTypeButton :comment="comment"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendCommentReaction="sendCommentReaction" />
    </div>
</template>
