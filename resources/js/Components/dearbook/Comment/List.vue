<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

const props = defineProps({
    post: Object,
    commentsList: Object,
    typeList: String,
});

const emit = defineEmits(['callOpenDetailModal', 'callOpenAttachmentsModal', 'callOpenUserReactionsModal', 'callRestartGeneralDataFromPostComments', 'callRestartPostCommentListToCommentBox', 'callConfirmDeletion', 'callActiveShowNotification',])

const openDetailModal = () => {
    emit('callOpenDetailModal')
}

const openAttachmentsModal = (comment, index, entityPrefix) => {
    emit('callOpenAttachmentsModal', comment, index, entityPrefix)
}

const openUserReactionsModal = (comment, tabIndex) => {
    emit("callOpenUserReactionsModal", comment, tabIndex);
};

const restartGeneralDataFromPostComments = (generalData) => {
    emit('callRestartGeneralDataFromPostComments', generalData)
}

const restartPostCommentListToCommentBox = (latestComments, allComments) => {
    emit("callRestartPostCommentListToCommentBox", latestComments, allComments);
};

const confirmDeletion = (comment, entityPrefix) => {
    emit("callConfirmDeletion", comment, entityPrefix);
};

const activeShowNotification = (errors) => {
    emit("callActiveShowNotification", errors);
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
                @callOpenAttachmentsModal="openAttachmentsModal" @callOpenUserReactionsModal="openUserReactionsModal"
                @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostComments"
                @callRestartPostCommentList="restartPostCommentListToCommentBox" @callConfirmDeletion="confirmDeletion"
                @callActiveShowNotification="activeShowNotification" />
        </div>
    </template>
</template>
