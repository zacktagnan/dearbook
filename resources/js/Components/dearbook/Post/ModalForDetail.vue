<script setup>
import BaseModal from '@/Components/dearbook/BaseModal.vue'
import CloseModal from '@/Components/dearbook/CloseModal.vue'
import {
    DialogTitle,
} from "@headlessui/vue";
import {
    XMarkIcon,
} from "@heroicons/vue/24/solid";
import PostDetailItem from '@/Components/dearbook/Post/Item.vue'
import { computed, ref } from "vue";

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    modelValue: Boolean,
});

const emit = defineEmits(["update:modelValue", 'callOpenEditModal', 'callArchiveItem', 'callOpenAttachmentsModal', 'callOpenUserReactionsModal', 'callConfirmDeletion', 'callActiveShowNotification']);

const showDetail = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const closeModalDetail = () => {
    showDetail.value = false;
}

const openAttachmentsModal = (entity, index, entityPrefix) => {
    emit('callOpenAttachmentsModal', entity, index, entityPrefix)
}

const openUserReactionsModal = (entity, tabIndex) => {
    emit("callOpenUserReactionsModal", entity, tabIndex);
};

const confirmDeletion = (entity, entityPrefix) => {
    emit("callConfirmDeletion", entity, entityPrefix);
}

const activeShowNotification = (errors) => {
    emit("callActiveShowNotification", errors);
};

const postDetailItemRef = ref(null)
const filterDeletedComment = (commentId) => {
    postDetailItemRef.value.filterDeletedComment(commentId)
}

defineExpose({
    filterDeletedComment,
})
</script>

<template>
    <BaseModal v-model="showDetail" :z-index="'z-[44]'" :dialog-panel-extra-classes="'max-w-2xl mt-11'">
        <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300 dark:border-gray-700">
            <div class="w-full text-center">
                <DialogTitle as="h3" class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ 'Publicación de ' + post.user.name }}
                </DialogTitle>
            </div>

            <CloseModal @call-close-modal="closeModalDetail" />
        </div>

        <div class="overflow-auto max-h-[747px]">
            <PostDetailItem ref="postDetailItemRef" :post="post" :type-list="'all'"
                @callOpenEditModal="$emit('callOpenEditModal', post)"
                @callArchiveItem="$emit('callArchiveItem', post.id, 'home')"
                @callOpenAttachmentsModal="openAttachmentsModal"
                @callOpenUserReactionsModal="openUserReactionsModal"
                @callConfirmDeletion="confirmDeletion"
                @callActiveShowNotification="activeShowNotification" />
        </div>
    </BaseModal>
</template>
