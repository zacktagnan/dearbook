<script setup>
import CommentLatestList from '@/Components/dearbook/Comment/LatestList.vue'
import CommentCreate from '@/Components/dearbook/Comment/Create.vue'
import { ref } from 'vue';

const commentCreateRef = ref(null);

import axiosClient from '@/axiosClient'

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToItem'])

const focusCommentTextAreaOfCreate = () => {
    commentCreateRef.value.focusCommentTextArea()
};

const resetCommentTextAreaOfCreate = () => {
    commentCreateRef.value.resetCommentTextArea()
};

const sendComment = (comment) => {
    axiosClient.post(route('post.comment.store', props.post), {
        comment: comment,
    })
        .then(({ data }) => {
            props.post.total_of_comments = data.total_of_comments
            props.post.current_user_has_comment = data.current_user_has_comment
            props.post.current_user_total_of_comments = data.current_user_total_of_comments
            props.post.latest_comments = data.latest_comments
            resetCommentTextAreaOfCreate()
        })
        .catch((error) => {
            emit('callActiveShowNotificationToItem', error.response.data.errors)
        })
}

const activeShowNotificationToItem = (errors) => {
    emit("callActiveShowNotificationToItem", errors);
};

defineExpose({
    focusCommentTextAreaOfCreate,
})
</script>

<template>
    <CommentLatestList :post="post" @callActiveShowNotificationToCommentBox="activeShowNotificationToItem" />

    <CommentCreate ref="commentCreateRef" @callSendComment="sendComment" />
</template>
