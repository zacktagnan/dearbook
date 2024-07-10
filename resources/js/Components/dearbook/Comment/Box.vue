<script setup>
import CommentLatestList from '@/Components/dearbook/Comment/LatestList.vue'
import CommentCreate from '@/Components/dearbook/Comment/Create.vue'
import { ref } from 'vue';

const commentCreateRef = ref(null);

import axiosClient from '@/axiosClient'

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToItem', 'callOpenUserReactionsModalToItem',])

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
        comment: comment,
        attachments: attachments,
    }, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(({ data }) => {
            props.post.total_of_comments = data.total_of_comments
            props.post.current_user_has_comment = data.current_user_has_comment
            props.post.current_user_total_of_comments = data.current_user_total_of_comments
            props.post.latest_comments = data.latest_comments
            resetCommentDataOfCreate()
            reInitAdjustHeightTextAreaOfCreate()
        })
        .catch((error) => {
            processErrors(error.response.data.errors)
        })
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

const openUserReactionsModalToItem = (comment, tabIndex) => {
    emit("callOpenUserReactionsModalToItem", comment, tabIndex);
}

const activeShowNotificationToItem = (errors) => {
    emit("callActiveShowNotificationToItem", errors);
};

defineExpose({
    focusCommentTextAreaOfCreate,
})
</script>

<template>
    <CommentLatestList :post="post"
        @callOpenUserReactionsModalToCommentBox="openUserReactionsModalToItem"
        @callActiveShowNotificationToCommentBox="activeShowNotificationToItem" />

    <CommentCreate ref="commentCreateRef" @callSendComment="sendComment" />
</template>
