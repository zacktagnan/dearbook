<script setup>
import ReactionOtherTypeButton from '@/Components/dearbook/Comment/Reaction/OtherTypeButton.vue'

const props = defineProps({
    showReactionTypeBar: Boolean,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendCommentReaction'])

const sendCommentReaction = (from, type) => {
    emit('callSendCommentReaction', from, type)
}
</script>

<template>
    <div class="z-[22] opacity-0 scale-0 absolute left-3 lg:-left-2 bottom-4 p-[3px] border rounded-full shadow flex items-center gap-0.5 bg-white h-10 transition-opacity duration-500 delay-500 ease-in-out"
        :class="{
            'opacity-100 scale-110': showReactionTypeBar
        }" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)">
        <ReactionOtherTypeButton :title="'Me gusta'" :type="'like'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me encanta'" :type="'love'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me importa'" :type="'care'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me divierte'" :type="'haha'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me asombra'" :type="'wow'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me entristece'" :type="'sad'" @callSendCommentReaction="sendCommentReaction" />
        <ReactionOtherTypeButton :title="'Me enoja'" :type="'angry'" @callSendCommentReaction="sendCommentReaction" />
    </div>
</template>
