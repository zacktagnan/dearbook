<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

defineProps({
    post: Object,
});

const emit = defineEmits(['callOpenAttachmentsModalToCommentBox', 'callOpenUserReactionsModalToCommentBox', 'callConfirmDeletionToCommentBox', 'callActiveShowNotificationToCommentBox',])

const openAttachmentsModalToCommentBox = (comment, index, entityPrefix) => {
    emit('callOpenAttachmentsModalToCommentBox', comment, index, entityPrefix)
}

const openUserReactionsModalToCommentBox = (comment, tabIndex) => {
    emit("callOpenUserReactionsModalToCommentBox", comment, tabIndex);
};

const confirmDeletionToCommentBox = (comment, entityPrefix) => {
    emit("callConfirmDeletionToCommentBox", comment, entityPrefix);
};

const activeShowNotificationToCommentBox = (errors) => {
    emit("callActiveShowNotificationToCommentBox", errors);
};
</script>

<template>
    <button v-if="post.total_of_comments > 1" class="text-[15px] font-bold text-gray-500 hover:underline mt-2" title="Todos los comentarios disponibles">Ver más comentarios</button>

    <template v-if="post.latest_comments.length > 0">
        <div v-for="latest_comment of post.latest_comments">
            <CommentItem :comment="latest_comment"
                @callOpenAttachmentsModalToLatestList="openAttachmentsModalToCommentBox"
                @callOpenUserReactionsModalToLatestList="openUserReactionsModalToCommentBox"
                @callConfirmDeletionToLatestList="confirmDeletionToCommentBox"
                @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />
        </div>
    </template>
</template>
