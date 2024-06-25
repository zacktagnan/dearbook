<script setup>
import CommentCreate from '@/Components/dearbook/Comment/Create.vue'
import { ref } from 'vue';

const commentCreateRef = ref(null);

import axiosClient from '@/axiosClient'

const props = defineProps({
    post: Object,
});

const focusCommentTextAreaOfCreate = () => {
    commentCreateRef.value.focusCommentTextArea()
};

const sendComment = (comment) => {
    // console.log('Alohaaaaa')
    // console.log(comment)
    // alert('Se envió un comentario...')
    axiosClient.post(route('post.comment.store', props.post), {
        comment: comment,
    })
        .then(({ data }) => {
            props.post.total_of_comments = data.total_of_comments
        })
        .catch((error) => {
            // console.log('ERRORES: ', error.response.data.errors)
            emit('callActiveShowNotificationToItem', error.response.data.errors)
        })
    // resetear TextareaInput si fuera necesario...
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
