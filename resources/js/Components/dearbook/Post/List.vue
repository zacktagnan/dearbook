<script setup>
import PostItem from '@/Components/dearbook/Post/Item.vue';
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import PostDetailModal from "@/Components/dearbook/Post/ModalForDetail.vue";
import AttachmentModal from "@/Components/dearbook/Attachment/Modal.vue";
import UserReactionsModal from '@/Components/dearbook/Reaction/Modal.vue'
import ConfirmPostDeletionModal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { router, usePage } from "@inertiajs/vue3";
import { ref } from 'vue';

defineProps({
    'posts': Array,
})

const showEditModal = ref(false)
const showDetailModal = ref(false)
const showAttachmentsModal = ref(false)
const showReactionsModal = ref(false)
const tabIndexReactionsModal = ref(0)

const postToEdit = ref({})
const postDetail = ref({})
const entityWithAttachmentsToPreview = ref({})
const entityWithReactions = ref({})

const openEditModal = (post) => {
    postToEdit.value = post
    showEditModal.value = true
}

const openDetailModal = (post) => {
    postDetail.value = post
    showDetailModal.value = true
}

const openAttachmentsModal = (entity, index, entityPrefix) => {
    entityWithAttachmentsToPreview.value = {
        entity,
        index,
        entityPrefix,
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

const postDetailModalRef = ref(null)
const entityToDelete = ref({})
const showingConfirmDeletion = ref(false);
const showConfirmDeletion = (entity, entityPrefix) => {
    entityToDelete.value = {
        entity,
        entityPrefix,
    }
    showingConfirmDeletion.value = true;
};
const closeConfirmDeletion = () => {
    showingConfirmDeletion.value = false;
};

const deleteEntity = () => {
    if (entityToDelete.value.entityPrefix === 'post.comment') {
        entityToDelete.value.entity = entityToDelete.value.entity.id
    }
    router.delete(route(entityToDelete.value.entityPrefix + ".destroy", entityToDelete.value.entity), {
        preserveScroll: true,
        onSuccess: () => {
            closeConfirmDeletion()

            if (showDetailModal.value === true) {
                // if (entityToDelete.value.entityPrefix === 'post') {
                //     showDetailModal.value = false
                // } else if (entityToDelete.value.entityPrefix === 'post.comment') {
                //     postDetailModalRef.value.filterDeletedComment(entityToDelete.value.entity)
                // }
                if (entityToDelete.value.entityPrefix === 'post.comment') {
                    postDetailModalRef.value.filterDeletedComment(entityToDelete.value.entity)
                }
                showDetailModal.value = false
            }
        },
    });
};

// -------------------------------------

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const errorsFromPost = ref({})

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const timer = ref(null)
const timeOnSeconds = ref(0)
const maxTimeOnSecondsForNotificationBox = usePage().props.maxTimeOnSecondsForNotificationBox;

const activeShowNotification = (errors) => {
    errorsFromPost.value = errors
    showNotification.value = true

    // setTimeout(() => {
    //     closingNotification('notification')
    // }, 3000)
    timer.value = null
    timeOnSeconds.value = 0
    startClosingNotification()
}

const startClosingNotification = () => {
    timer.value = setInterval(() => {
        // if (notificationBoxRef.value) {
        //     timeOnSeconds.value++
        //     console.log('timeOnSeconds', timeOnSeconds.value)

        //     if (timeOnSeconds.value >= maxTimeOnSecondsForNotificationBox) {
        //         stopClosingNotification()
        //         closingNotification('notification')
        //         console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        //     }
        // }
        if (notificationBoxRef.value && timeOnSeconds.value < maxTimeOnSecondsForNotificationBox) {
            timeOnSeconds.value++
            console.log('timeOnSeconds', timeOnSeconds.value)
        } else {
            stopClosingNotification()
            closingNotification('notification')
            console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        }
    }, 1000)
}

const stopClosingNotification = () => {
    clearInterval(timer.value)
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
            @callOpenDetailModal="openDetailModal" @callOpenAttachmentsModal="openAttachmentsModal"
            @callOpenUserReactionsModal="openUserReactionsModal" @callConfirmDeletion="showConfirmDeletion"
            @callActiveShowNotification="activeShowNotification" />

        <PostModal :post="postToEdit" v-model="showEditModal" @callActiveShowNotification="activeShowNotification" />

        <PostDetailModal ref="postDetailModalRef" :post="postDetail" v-model="showDetailModal"
            @callOpenEditModal="openEditModal" @callOpenAttachmentsModal="openAttachmentsModal"
            @callOpenUserReactionsModal="openUserReactionsModal" @callConfirmDeletion="showConfirmDeletion"
            @callActiveShowNotification="activeShowNotification" />

        <AttachmentModal :attachments="entityWithAttachmentsToPreview.entity?.attachments || []"
            :entity-prefix="entityWithAttachmentsToPreview.entityPrefix"
            v-model:index="entityWithAttachmentsToPreview.index" v-model="showAttachmentsModal" />

        <UserReactionsModal v-model="showReactionsModal" :entity="entityWithReactions"
            :default-index="tabIndexReactionsModal" />

        <ConfirmPostDeletionModal :show="showingConfirmDeletion" @close="closeConfirmDeletion">
            <div class="p-6">
                <template v-if="entityToDelete.entityPrefix === 'post'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $t('dearbook.post.index.confirm_deletion.question') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t('dearbook.post.index.confirm_deletion.message') }}
                    </p>
                </template>
                <template v-else-if="entityToDelete.entityPrefix === 'post.comment'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $t('dearbook.comment.index.confirm_deletion.question') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t('dearbook.comment.index.confirm_deletion.message') }}
                    </p>
                </template>

                <div class="flex justify-end mt-6">
                    <SecondaryButton @click="closeConfirmDeletion" :title="$t('Cancel')"> {{ $t('Cancel') }}
                    </SecondaryButton>

                    <DangerButton v-if="entityToDelete.entityPrefix === 'post'" class="ms-3" @click="deleteEntity"
                        :title="$t('dearbook.post.index.confirm_deletion.button_text')">
                        {{ $t('dearbook.post.index.confirm_deletion.button_text') }}
                    </DangerButton>
                    <DangerButton v-else-if="entityToDelete.entityPrefix === 'post.comment'" class="ms-3"
                        @click="deleteEntity" :title="$t('dearbook.comment.index.confirm_deletion.button_text')">
                        {{ $t('dearbook.comment.index.confirm_deletion.button_text') }}
                    </DangerButton>
                </div>
            </div>
        </ConfirmPostDeletionModal>

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-if="showNotification && errorsFromPost.body" :title="'Error'" :message="errorsFromPost.body" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.attachments" :title="'Error'"
            :message="errorsFromPost.attachments" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.reaction_type" :title="'Error'"
            :message="errorsFromPost.reaction_type" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.comment" :title="'Error'" :message="errorsFromPost.comment" />
    </div>
</template>
