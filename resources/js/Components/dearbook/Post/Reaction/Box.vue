<script setup>
import ReactionTypeBar from '@/Components/dearbook/Post/Reaction/TypeBar.vue'
import ReactionMainTypeButton from '@/Components/dearbook/Post/Reaction/MainTypeButton.vue'
import { ref } from "vue";

import axiosClient from '@/axiosClient'

const showReactionTypeBar = ref(false)

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callRestartDefaultTabIndex', 'callActiveShowNotificationToItem'])

const changeShowReactionTypeBar = (value) => {
    showReactionTypeBar.value = value
}

const sendPostReaction = (from, type) => {
    axiosClient.post(route('post.reaction', props.post), {
        from_main_reaction_button: from,
        reaction_type: type,
        current_reaction_type: props.post.current_user_type_reaction,
    })
        .then(({ data }) => {
            props.post.current_user_type_reaction = data.current_user_type_reaction
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.total_of_reactions = data.total_of_reactions

            props.post.all_user_reactions = data.all_user_reactions
            props.post.like_user_reactions = data.like_user_reactions
            props.post.love_user_reactions = data.love_user_reactions
            props.post.care_user_reactions = data.care_user_reactions
            props.post.haha_user_reactions = data.haha_user_reactions
            props.post.wow_user_reactions = data.wow_user_reactions
            props.post.sad_user_reactions = data.sad_user_reactions
            props.post.angry_user_reactions = data.angry_user_reactions
            emit('callRestartDefaultTabIndex')
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
    emit("callActiveShowNotificationToItem", errorProcessed);
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
    <div class="relative w-1/2">
        <ReactionTypeBar :showReactionTypeBar="showReactionTypeBar"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendPostReaction="sendPostReaction" />

        <ReactionMainTypeButton :post="post"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendPostReaction="sendPostReaction" />
    </div>
</template>
