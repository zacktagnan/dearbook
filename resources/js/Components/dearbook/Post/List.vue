<script setup>
import PostItem from '@/Components/dearbook/Post/Item.vue';
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import AttachmentModal from "@/Components/dearbook/Attachment/Modal.vue";
import UserReactionsModal from '@/Components/dearbook/Reaction/Modal.vue'
import ConfirmPostDeletionModal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { router } from "@inertiajs/vue3";
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
        'all': entity.all_user_reactions,
        'like': entity.like_user_reactions,
        'love': entity.love_user_reactions,
        'care': entity.care_user_reactions,
        'haha': entity.haha_user_reactions,
        'wow': entity.wow_user_reactions,
        'sad': entity.sad_user_reactions,
        'angry': entity.angry_user_reactions,
    }
    showReactionsModal.value = true
    tabIndexReactionsModal.value = tabIndex
}

const postToDelete = ref({})
const showingConfirmPostDeletion = ref(false);
const showConfirmPostDeletion = (post) => {
    postToDelete.value = post
    showingConfirmPostDeletion.value = true;
};
const closeConfirmPostDeletion = () => {
    showingConfirmPostDeletion.value = false;
};

const deletePost = () => {
    router.delete(route("post.destroy", postToDelete.value), {
        preserveScroll: true,
        onSuccess: () => closeConfirmPostDeletion(),
    });
};

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
        <PostItem v-for="post in posts" :post="post"
            @callOpenEditModal="openEditModal"
            @callOpenAttachmentsModal="openAttachmentsModal"
            @callOpenUserReactionsModal="openUserReactionsModal"
            @callConfirmPostDeletion="showConfirmPostDeletion"
            @callActiveShowNotificationFromItem="activeShowNotification" />

        <PostModal :post="postToEdit" v-model="showEditModal" @callActiveShowNotification="activeShowNotification" />

        <AttachmentModal :attachments="postWithAttachmentsToPreview.post?.attachments || []"
            v-model:index="postWithAttachmentsToPreview.index" v-model="showAttachmentsModal" />

        <UserReactionsModal v-model="showReactionsModal"
            :entity="entityWithReactions" :default-index="tabIndexReactionsModal" />

        <ConfirmPostDeletionModal :show="showingConfirmPostDeletion" @close="closeConfirmPostDeletion">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $t('dearbook.post.index.confirm_deletion.question') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('dearbook.post.index.confirm_deletion.message') }}
                </p>

                <div class="flex justify-end mt-6">
                    <SecondaryButton @click="closeConfirmPostDeletion" :title="$t('Cancel')"> {{ $t('Cancel') }} </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        @click="deletePost"
                        :title="$t('dearbook.post.index.confirm_deletion.button_text')"
                    >
                        {{ $t('dearbook.post.index.confirm_deletion.button_text') }}
                    </DangerButton>
                </div>
            </div>
        </ConfirmPostDeletionModal>

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.attachments" :title="'Error'"
            :message="errorsFromPost.attachments" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.reaction_type" :title="'Error'"
            :message="errorsFromPost.reaction_type" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPost.comment" :title="'Error'"
            :message="errorsFromPost.comment" />
    </div>
</template>
