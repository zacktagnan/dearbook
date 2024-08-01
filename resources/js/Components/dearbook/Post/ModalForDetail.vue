<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
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

const emit = defineEmits(["update:modelValue", 'callOpenEditModal', 'callOpenAttachmentsModal', 'callOpenUserReactionsModal', 'callConfirmDeletion', 'callActiveShowNotification']);

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
    <teleport to="body">
        <TransitionRoot appear :show="showDetail" as="template">
            <Dialog as="div" class="relative z-10">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                    enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-full p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                class="w-full max-w-2xl overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl mt-11">
                                <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300">
                                    <div class="w-full text-center">
                                        <DialogTitle as="h3" class="text-lg font-bold text-gray-900">
                                            {{ 'Publicación de ' + post.user.name }}
                                        </DialogTitle>
                                    </div>

                                    <button @click="closeModalDetail"
                                        class="flex items-center p-1 font-bold text-gray-500 transition-colors duration-200 bg-gray-200 rounded-full hover:bg-gray-300 hover:text-gray-700"
                                        title="Cerrar">
                                        <XMarkIcon class="w-5 h-5" />
                                    </button>
                                </div>

                                <div class="overflow-auto max-h-[747px]">
                                    <PostDetailItem ref="postDetailItemRef" :post="post" :type-list="'all'"
                                        @callOpenEditModal="$emit('callOpenEditModal', post)"
                                        @callOpenAttachmentsModal="openAttachmentsModal"
                                        @callOpenUserReactionsModal="openUserReactionsModal"
                                        @callConfirmDeletion="confirmDeletion"
                                        @callActiveShowNotification="activeShowNotification" />
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
