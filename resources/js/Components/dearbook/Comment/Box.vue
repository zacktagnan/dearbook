<script setup>
import CommentList from '@/Components/dearbook/Comment/List.vue'
import CommentCreate from '@/Components/dearbook/Comment/Create.vue'
import { ref, } from 'vue';

const commentCreateRef = ref(null);

import axiosClient from '@/axiosClient'

const props = defineProps({
    post: Object,
    commentsList: Object,
    typeList: String,
    createAction: String,
    parentId: {
        type: [String, Number],
        default: '',
    },
});

const emit = defineEmits(['callOpenDetailModalToItem', 'callOpenAttachmentsModalToItem', 'callOpenUserReactionsModalToItem', 'callRestartGeneralDataFromPostCommentsToItem', 'callRestartPostCommentListToItem', 'callConfirmDeletionToItem', 'callActiveShowNotificationToItem',])

const focusCommentTextAreaOfCreate = () => {
    commentCreateRef.value.focusCommentTextArea()
};

const resetCommentDataOfCreate = () => {
    commentCreateRef.value.resetCommentData()
};

const reInitAdjustHeightTextAreaOfCreate = () => {
    commentCreateRef.value.reInitAdjustHeightTextArea()
};

const sendComment = (comment, attachments) => {
    axiosClient.post(route('post.comment.store', props.post), {
        parent_id: props.parentId || null,
        comment: comment,
        attachments: attachments,
    }, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(({ data }) => {
            let generalDataFromPostComments = {
                total_of_comments: data.total_of_comments,
                current_user_has_comment: data.current_user_has_comment,
                current_user_total_of_comments: data.current_user_total_of_comments,

                all_child_comments: data.all_child_comments,
            }
            emit('callRestartGeneralDataFromPostCommentsToItem', generalDataFromPostComments)
            emit('callRestartPostCommentListToItem', data.latest_comments, data.all_comments)
            resetCommentDataOfCreate()
            reInitAdjustHeightTextAreaOfCreate()
        })
        .catch((error) => {
            processErrors(error.response.data.errors)
        })
}

const restartGeneralDataFromPostCommentsDeeper = (generalData) => {
    emit('callRestartGeneralDataFromPostCommentsToItem', generalData)
}

const restartPostCommentList = (latestComments, allComments) => {
    emit('callRestartPostCommentListToItem', latestComments, allComments)
}

const processErrors = (errors) => {
    let errorProcessed
    for (const key in errors) {
        if (key.includes(".")) {
            const [item, index] = key.split(".");
            errorProcessed = buildErrors(item, errors[key][0])
            if (item === 'attachments') {
                commentCreateRef.value.storingTheError(index, errors[key][0])
            }
        } else {
            errorProcessed = buildErrors(key, errors[key][0])
        }
    }
    emit("callActiveShowNotificationToItem", errorProcessed);
};

const buildErrors = (key, errorMsg) => {
    let errors
    switch (key) {
        case 'comment':
            errors = {
                comment: errorMsg,
            };
            break;
        case 'attachments':
            errors = {
                attachments: errorMsg,
            };
            break;
        default:
            break;
    }

    return errors;
};

const openDetailModalToItem = () => {
    emit("callOpenDetailModalToItem");
}

const openAttachmentsModalToItem = (comment, index, entityPrefix) => {
    emit("callOpenAttachmentsModalToItem", comment, index, entityPrefix);
}

const openUserReactionsModalToItem = (comment, tabIndex) => {
    emit("callOpenUserReactionsModalToItem", comment, tabIndex);
}

const confirmDeletionToItem = (comment, entityPrefix) => {
    emit("callConfirmDeletionToItem", comment, entityPrefix);
}

const activeShowNotificationToItem = (errors) => {
    emit("callActiveShowNotificationToItem", errors);
};

defineExpose({
    focusCommentTextAreaOfCreate,
})
</script>

<template>
    <CommentList :post="post" :comments-list="commentsList" :type-list="typeList"
        @callOpenDetailModalToCommentBox="openDetailModalToItem"
        @callOpenAttachmentsModalToCommentBox="openAttachmentsModalToItem"
        @callOpenUserReactionsModalToCommentBox="openUserReactionsModalToItem"
        @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostCommentsDeeper"
        @callRestartPostCommentListToCommentBox="restartPostCommentList"
        @callConfirmDeletionToCommentBox="confirmDeletionToItem"
        @callActiveShowNotificationToCommentBox="activeShowNotificationToItem" />

    <CommentCreate ref="commentCreateRef" :action="createAction" @callSendComment="sendComment" />
</template>
