<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

const props = defineProps({
    post: Object,
    commentsList: Object,
    typeList: String,
});

const emit = defineEmits(['callOpenDetailModal', 'callOpenAttachmentsModal', 'callOpenUserReactionsModalToCommentBox', 'callRestartGeneralDataFromPostComments', 'callRestartPostCommentListToCommentBox', 'callConfirmDeletionToCommentBox', 'callActiveShowNotificationToCommentBox',])

const openDetailModal = () => {
    emit('callOpenDetailModal')
}

const openAttachmentsModal = (comment, index, entityPrefix) => {
    emit('callOpenAttachmentsModal', comment, index, entityPrefix)
}

const openUserReactionsModalToCommentBox = (comment, tabIndex) => {
    emit("callOpenUserReactionsModalToCommentBox", comment, tabIndex);
};

const restartGeneralDataFromPostComments = (generalData) => {
    emit('callRestartGeneralDataFromPostComments', generalData)
}

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
    <button v-if="typeList === 'latest' && post.total_of_comments > 1" @click="openDetailModal"
        class="text-[15px] font-bold text-gray-500 hover:underline mt-2" title="Todos los comentarios disponibles">
        Ver más comentarios
    </button>

    <template v-if="commentsList.length > 0">
        <div v-for="comment_item of commentsList">
            <CommentItem :post="post" :comment="comment_item" :type-list="typeList"
                @callOpenAttachmentsModal="openAttachmentsModal"
                @callOpenUserReactionsModalToLatestList="openUserReactionsModalToCommentBox"
                @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostComments"
                @callRestartPostCommentList="restartPostCommentListToCommentBox"
                @callConfirmDeletionToLatestList="confirmDeletionToCommentBox"
                @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />
        </div>
    </template>
</template>
