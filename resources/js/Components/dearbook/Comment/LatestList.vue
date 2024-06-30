<script setup>
import CommentItem from '@/Components/dearbook/Comment/Item.vue'

defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToCommentBox'])

const activeShowNotificationToCommentBox = (errors) => {
    emit("callActiveShowNotificationToCommentBox", errors);
};
</script>

<template>
    <template v-if="post.latest_comments.length > 0">
        <div v-for="latest_comment of post.latest_comments" class="flex gap-2 mt-3">
            <CommentItem :comment="latest_comment" @callActiveShowNotificationToLatestList="activeShowNotificationToCommentBox" />
        </div>
    </template>
</template>
