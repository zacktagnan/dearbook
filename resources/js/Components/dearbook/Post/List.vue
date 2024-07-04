<script setup>
import PostItem from '@/Components/dearbook/Post/Item.vue';
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import AttachmentModal from "@/Components/dearbook/Attachment/Modal.vue";
import UserReactionsModal from '@/Components/dearbook/Reaction/Modal.vue'
import { ref } from 'vue';

defineProps({
    'posts': Array,
})

const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const showReactionsModal = ref(false)
const tabIndexReactionsModal = ref(0)

const postToEdit = ref({})
const postWithAttachmentsToPreview = ref({})
const entityWithReactions = ref({})

const openEditModal = (post) => {
    postToEdit.value = post
    showEditModal.value = true
}

const openAttachmentsModal = (post, index) => {
    postWithAttachmentsToPreview.value = {
        post,
        index,
    }
    showAttachmentsModal.value = true
}

const openUserReactionsModal = (entity, tabIndex) => {
    // entityWithReactions.value = entity
    entityWithReactions.value = {
        'current_user_has_reaction': entity.current_user_has_reaction,
        'current_user_type_reaction': entity.current_user_type_reaction,
        'all': entity.all_reactions_users,
        'like': entity.like_reactions_users,
        'love': entity.love_reactions_users,
        'care': entity.care_reactions_users,
        'haha': entity.haha_reactions_users,
        'wow': entity.wow_reactions_users,
        'sad': entity.sad_reactions_users,
        'angry': entity.angry_reactions_users,
    }
    showReactionsModal.value = true
    tabIndexReactionsModal.value = tabIndex
}

// -------------------------------------

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const errorsFromPost = ref({})

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const activeShowNotification = (errors) => {
    errorsFromPost.value = errors
    showNotification.value = true

    setTimeout(() => {
        closingNotification('notification')
    }, 3000)
}

const closingNotification = (className) => {
    // Siempre que no se haya cerrado ya, manualmente, la notificación
    // (o sea, que exista, es decir, que aún no sea NULL)
    if (notificationBoxRef.value) {
        notificationBoxRef.value.fadeOutEffect(className)
    }
}

const closeShowNotification = () => {
    showNotification.value = false
}
</script>

<template>
    <div>
        <PostItem v-for="post in posts" :post="post" @callOpenEditModal="openEditModal"
            @callOpenAttachmentsModal="openAttachmentsModal"
            @callOpenUserReactionsModal="openUserReactionsModal"
            @callActiveShowNotificationFromItem="activeShowNotification" />

        <PostModal :post="postToEdit" v-model="showEditModal" @callActiveShowNotification="activeShowNotification" />

        <AttachmentModal :attachments="postWithAttachmentsToPreview.post?.attachments || []"
            v-model:index="postWithAttachmentsToPreview.index" v-model="showAttachmentsModal" />

        <UserReactionsModal v-model="showReactionsModal"
            :entity="entityWithReactions" :default-index="tabIndexReactionsModal" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.attachments" :title="'Error'"
            :message="errorsFromPost.attachments" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.reaction_type" :title="'Error'"
            :message="errorsFromPost.reaction_type[0]" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.comment" :title="'Error'"
            :message="errorsFromPost.comment[0]" />
    </div>
</template>
