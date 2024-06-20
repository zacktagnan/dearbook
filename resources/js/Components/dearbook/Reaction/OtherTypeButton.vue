<script setup>
const props = defineProps({
    title: String,
    type: String,
});

const emit = defineEmits(['callSendReaction'])
</script>

<template>
    <div class="tooltip tooltip-top" :data-tip="title">
        <button @click="$emit('callSendReaction', false, type)" class="flex justify-center w-10 group">
            <img :src="'/img/emojis/' + type + '.png'" :alt="title"
                class="w-[35px] h-[35px] group-hover:w-[39px] group-hover:h-[39px] transition-all duration-150" />
        </button>
    </div>
</template>
