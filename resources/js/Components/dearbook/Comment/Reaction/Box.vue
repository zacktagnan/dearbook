<script setup>
import ReactionTypeBar from '@/Components/dearbook/Comment/Reaction/TypeBar.vue'
import ReactionMainTypeButton from '@/Components/dearbook/Comment/Reaction/MainTypeButton.vue'
import { ref } from "vue";

import axiosClient from '@/axiosClient'

const showReactionTypeBar = ref(false)

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['callRestartDefaultTabIndex', 'callRestartPostCommentList', 'callActiveShowNotificationToCommentItem'])

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

            props.comment.all_user_reactions = data.all_user_reactions
            props.comment.like_user_reactions = data.like_user_reactions
            props.comment.love_user_reactions = data.love_user_reactions
            props.comment.care_user_reactions = data.care_user_reactions
            props.comment.haha_user_reactions = data.haha_user_reactions
            props.comment.wow_user_reactions = data.wow_user_reactions
            props.comment.sad_user_reactions = data.sad_user_reactions
            props.comment.angry_user_reactions = data.angry_user_reactions

            emit('callRestartDefaultTabIndex')

            emit('callRestartPostCommentList', data.latest_comments, data.all_comments)
        })
        .catch((error) => {
            processErrors(error.response.data.errors)
        })
    changeShowReactionTypeBar(false)
}

const processErrors = (errors) => {
    let errorProcessed
    for (const key in errors) {
        errorProcessed = buildErrors(key, errors[key][0])
    }
    emit("callActiveShowNotificationToCommentItem", errorProcessed);
};

const buildErrors = (key, errorMsg) => {
    let errors
    switch (key) {
        case 'reaction_type':
            errors = {
                reaction_type: errorMsg,
            };
            break;
        default:
            break;
    }

    return errors;
};
</script>

<template>
    <div class="relative">
        <ReactionTypeBar :showReactionTypeBar="showReactionTypeBar"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendCommentReaction="sendCommentReaction" />

        <ReactionMainTypeButton :comment="comment"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendCommentReaction="sendCommentReaction" />
    </div>
</template>
