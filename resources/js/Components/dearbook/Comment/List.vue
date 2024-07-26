<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

const props = defineProps({
    commentsList: Array,
    totalOfComments: Number,
    typeList: String,
});

const emit = defineEmits(['callOpenDetailModalToCommentBox', 'callOpenAttachmentsModalToCommentBox', 'callOpenUserReactionsModalToCommentBox', 'callRestartPostCommentListToCommentBox', 'callConfirmDeletionToCommentBox', 'callActiveShowNotificationToCommentBox',])

const openDetailModalToCommentBox = () => {
    emit('callOpenDetailModalToCommentBox')
}

const openAttachmentsModalToCommentBox = (comment, index, entityPrefix) => {
    emit('callOpenAttachmentsModalToCommentBox', comment, index, entityPrefix)
}

const openUserReactionsModalToCommentBox = (comment, tabIndex) => {
    emit("callOpenUserReactionsModalToCommentBox", comment, tabIndex);
};

const restartPostCommentListToCommentBox = (latestComments, allComments) => {
    emit("callRestartPostCommentListToCommentBox", latestComments, allComments);
};

const confirmDeletionToCommentBox = (comment, entityPrefix) => {
    emit("callConfirmDeletionToCommentBox", comment, entityPrefix);
};

const activeShowNotificationToCommentBox = (errors) => {
    emit("callActiveShowNotificationToCommentBox", errors);
};
</script>

<template>
    <button v-if="typeList === 'latest' && totalOfComments > 1" @click="openDetailModalToCommentBox"
        class="text-[15px] font-bold text-gray-500 hover:underline mt-2" title="Todos los comentarios disponibles">
        Ver más comentarios
    </button>

    <template v-if="commentsList.length > 0">
        <div v-for="comment_item of commentsList">
            <CommentItem :comment="comment_item" :type-list="typeList"
                @callOpenAttachmentsModalToLatestList="openAttachmentsModalToCommentBox"
                @callOpenUserReactionsModalToLatestList="openUserReactionsModalToCommentBox"
                @callRestartPostCommentList="restartPostCommentListToCommentBox"
                @callConfirmDeletionToLatestList="confirmDeletionToCommentBox"
                @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />
        </div>
    </template>
</template>
