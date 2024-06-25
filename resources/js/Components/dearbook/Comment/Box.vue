<script setup>
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
            resetCommentTextAreaOfCreate()
        })
        .catch((error) => {
            emit('callActiveShowNotificationToItem', error.response.data.errors)
        })
}

defineExpose({
    focusCommentTextAreaOfCreate,
})
</script>

<template>
    <div>
        Listado
    </div>

    <CommentCreate ref="commentCreateRef" @callSendComment="sendComment" />
</template>
