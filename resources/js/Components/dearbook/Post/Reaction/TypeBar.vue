<script setup>
import ReactionOtherTypeButton from '@/Components/dearbook/Post/Reaction/OtherTypeButton.vue'

const props = defineProps({
    showReactionTypeBar: Boolean,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendPostReaction'])

const sendPostReaction = (from, type) => {
    emit('callSendPostReaction', from, type)
}
</script>

<template>
    <div class="z-[22] opacity-0 scale-0 absolute left-3 lg:left-10 bottom-[38px] p-[3px] border dark:border-slate-700 rounded-full shadow flex items-center gap-1.5 bg-white dark:bg-slate-700 h-12 transition-opacity duration-500 delay-500 ease-in-out"
        :class="{
            'opacity-100 scale-110': showReactionTypeBar
        }" @mouseover="$emit('callChangeShowReactionTypeBar', true)"
        @mouseleave="$emit('callChangeShowReactionTypeBar', false)">
        <ReactionOtherTypeButton :title="'Me gusta'" :type="'like'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me encanta'" :type="'love'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me importa'" :type="'care'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me divierte'" :type="'haha'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me asombra'" :type="'wow'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me entristece'" :type="'sad'" @callSendPostReaction="sendPostReaction" />
        <ReactionOtherTypeButton :title="'Me enoja'" :type="'angry'" @callSendPostReaction="sendPostReaction" />
    </div>
</template>
