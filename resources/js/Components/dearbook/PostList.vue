<script setup>
import PostItem from '@/Components/dearbook/PostItem.vue';

defineProps({
    'posts': Array,
})
</script>

<template>
    <div>
        <PostItem v-for="post in posts" :post="post" />
    </div>
</template>
