<script setup>
defineProps({
    totalOfComments: {
        type: Number,
        default: 0,
    },
})
</script>

<template>
    <div>
        <p v-if="totalOfComments > 0">{{ totalOfComments }} {{ totalOfComments === 1 ? 'comentario' : 'comentarios' }}</p>
    </div>
</template>
