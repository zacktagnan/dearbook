<script setup>
import ReactionTypeBar from '@/Components/dearbook/Reaction/TypeBar.vue'
import ReactionMainTypeButton from '@/Components/dearbook/Reaction/MainTypeButton.vue'
import { ref } from "vue";

import axiosClient from '@/axiosClient'

const showReactionTypeBar = ref(false)

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToItem'])

const changeShowReactionTypeBar = (value) => {
    showReactionTypeBar.value = value
}

// const sendReaction = () => {
const sendReaction = (from, type) => {
    axiosClient.post(route('post.reaction', props.post), {
        from_main_reaction_button: from,
        reaction_type: type,
        current_reaction_type: props.post.current_user_type_reaction,
    })
        .then(({ data }) => {
            props.post.current_user_type_reaction = data.current_user_type_reaction
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.total_of_reactions = data.total_of_reactions
        })
        .catch((error) => {
            // console.log('ERRORES: ', error.response.data.errors)
            emit('callActiveShowNotificationToItem', error.response.data.errors)
        })
    changeShowReactionTypeBar(false)
}
</script>

<template>
    <div class="relative w-1/2">
        <ReactionTypeBar :showReactionTypeBar="showReactionTypeBar"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendReaction="sendReaction" />

        <ReactionMainTypeButton :post="post"
            @callChangeShowReactionTypeBar="changeShowReactionTypeBar" @callSendReaction="sendReaction" />
    </div>
</template>
