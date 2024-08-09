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
    allChildCommentsTotal: {
        type: Number,
        default: 0,
    },
    showMoreCommentsLink: {
        type: Boolean,
        default: true,
    },
    isTrashed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['callOpenDetailModal', 'callOpenAttachmentsModal', 'callOpenUserReactionsModal', 'callRestartGeneralDataFromPostComments', 'callRestartPostCommentList', 'callConfirmDeletion', 'callActiveShowNotification',])

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
            emit('callRestartGeneralDataFromPostComments', generalDataFromPostComments)
            emit('callRestartPostCommentList', data.latest_comments, data.all_comments)
            resetCommentDataOfCreate()
            reInitAdjustHeightTextAreaOfCreate()
        })
        .catch((error) => {
            processErrors(error.response.data.errors)
        })
}

const restartGeneralDataFromPostCommentsDeeper = (generalData) => {
    emit('callRestartGeneralDataFromPostComments', generalData)
}

const restartPostCommentList = (latestComments, allComments) => {
    emit('callRestartPostCommentList', latestComments, allComments)
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
    emit("callActiveShowNotification", errorProcessed);
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

const openDetailModal = () => {
    emit("callOpenDetailModal");
}

const openAttachmentsModal = (comment, index, entityPrefix) => {
    emit("callOpenAttachmentsModal", comment, index, entityPrefix);
}

const openUserReactionsModal = (comment, tabIndex) => {
    emit("callOpenUserReactionsModal", comment, tabIndex);
}

const confirmDeletion = (comment, entityPrefix) => {
    emit("callConfirmDeletion", comment, entityPrefix);
}

const activeShowNotification = (errors) => {
    emit("callActiveShowNotification", errors);
};

defineExpose({
    focusCommentTextAreaOfCreate,
})
</script>

<template>
    <CommentList :post="post" :comments-list="commentsList" :type-list="typeList"
        :show-more-comments-link="showMoreCommentsLink" @callOpenDetailModal="openDetailModal"
        @callOpenAttachmentsModal="openAttachmentsModal" @callOpenUserReactionsModal="openUserReactionsModal"
        @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostCommentsDeeper"
        @callRestartPostCommentList="restartPostCommentList" @callConfirmDeletion="confirmDeletion"
        @callActiveShowNotification="activeShowNotification" />

    <button v-if="typeList === 'latest' && allChildCommentsTotal > 1" @click="openDetailModal"
        class="text-[15px] font-bold text-gray-500 hover:underline mt-2" title="Todas las respuestas disponibles">
        Ver más respuestas
    </button>

    <CommentCreate v-if="!isTrashed" ref="commentCreateRef" :action="createAction" @callSendComment="sendComment" />
</template>
