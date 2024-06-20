<script setup>
import ReactionOtherTypeButton from '@/Components/dearbook/Reaction/OtherTypeButton.vue'

const props = defineProps({
    showReactionTypeBar: Boolean,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendReaction'])

const sendReaction = (from, type) => {
    emit('callSendReaction', from, type)
}
</script>

<template>
    <div class="z-[22] opacity-0 scale-0 absolute left-3 lg:left-10 bottom-[38px] p-[3px] border rounded-full shadow flex items-center gap-1.5 bg-white h-12 transition-opacity duration-500 delay-500 ease-in-out"
        :class="{
            'opacity-100 scale-110': showReactionTypeBar
        }" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)">
        <ReactionOtherTypeButton :title="'Me gusta'" :type="'like'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me encanta'" :type="'love'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me importa'" :type="'care'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me divierte'" :type="'haha'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me asombra'" :type="'wow'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me entristece'" :type="'sad'" @callSendReaction="sendReaction" />
        <ReactionOtherTypeButton :title="'Me enoja'" :type="'angry'" @callSendReaction="sendReaction" />
    </div>
</template>
