<script setup>
import PostItem from '@/Components/dearbook/Post/Item.vue';
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import { ref } from 'vue';

defineProps({
    'posts': Array,
})

const showEditModal = ref(false)

const postToEdit = ref({})

const openEditModal = (post) => {
    postToEdit.value = post
    showEditModal.value = true
}
</script>

<template>
    <div>
        <PostItem v-for="post in posts" :post="post" @callOpenEditModal="openEditModal" />

        <PostModal :post="postToEdit" v-model="showEditModal" />
    </div>
</template>
