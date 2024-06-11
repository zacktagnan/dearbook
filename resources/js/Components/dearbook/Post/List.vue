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

// -------------------------------------

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const errorsFromPostToEdit = ref({})

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const activeShowNotification = (errors) => {
    errorsFromPostToEdit.value = errors
    // console.log('errorsFromPostToEdit', errorsFromPostToEdit)
    showNotification.value = true

    setTimeout(() => {
        closingNotification('notification')
    }, 3000)
}

const closingNotification = (className) => {
    notificationBoxRef.value.fadeOutEffect(className)
}

const closeShowNotification = () => {
    showNotification.value = false
}
</script>

<template>
    <div>
        <PostItem v-for="post in posts" :post="post" @callOpenEditModal="openEditModal" />

        <PostModal :post="postToEdit" v-model="showEditModal" @callActiveShowNotification="activeShowNotification" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPostToEdit.attachments" :title="'Error'"
            :message="errorsFromPostToEdit.attachments" />
    </div>
</template>
