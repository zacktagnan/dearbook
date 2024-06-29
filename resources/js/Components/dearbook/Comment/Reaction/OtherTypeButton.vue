<script setup>
const props = defineProps({
    title: String,
    type: String,
});

const emit = defineEmits(['callSendCommentReaction'])
</script>

<template>
    <div class="tooltip tooltip-top" :data-tip="title">
        <button @click="$emit('callSendCommentReaction', false, type)" class="flex justify-center w-8 group">
            <img :src="'/img/emojis/' + type + '.png'" :alt="title"
                class="transition-all duration-150 w-7 h-7 group-hover:w-8 group-hover:h-8" />
        </button>
    </div>
</template>
