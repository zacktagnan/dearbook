<script setup>
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { MenuItem } from "@headlessui/vue";
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';
import CommentReactionBox from '@/Components/dearbook/Comment/Reaction/Box.vue'
import CommentReactionTypeUsersSummary from "@/Components/dearbook/Comment/Reaction/TypeUsersSummary.vue";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted, nextTick } from "vue";
import { reactionTypesFormat, } from "@/Libs/helpers";

import CommentEdit from '@/Components/dearbook/Comment/Edit.vue'
import CommentAttachments from '@/Components/dearbook/Comment/Attachments.vue'

import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
} from '@headlessui/vue'

const props = defineProps({
    post: Object,
    comment: Object,
    typeList: String,
});

const maxCommentBodyLength = 100;

onMounted(() => {
    setTypeUserReactionsCommentByType()
});

const setTypeUserReactionsCommentByType = () => {
    defaultTabIndex.value = 0
    defaultTabIndexObject.value = {}

    Object.keys(reactionTypesFormat).forEach(type => {
        if (type !== 'all') {
            typeUserReactionsComment(type)
        }
    });
}

const authUser = usePage().props.auth.user;

const isTrashed = computed(
    () => props.post.deleted_at !== ''
);

const isArchived = computed(
    () => props.post.archived_at !== ''
);

// const isCommentAuthor = () => authUser && authUser.id === props.comment.user.id
// Sin el COMPUTED, funciona y debe ser llamado como método >> isCommentAuthor()
// Con el COMPUTED, funciona como una simple variable computada y debe ser llamado como tal
const isCommentAuthorAndEditable = computed(
    () => authUser && authUser.id === props.comment.user.id && !isTrashed.value && !isArchived.value
)

const defaultTabIndex = ref(0)
const defaultTabIndexObject = ref({})

const setDefaultTabIndex = (data, type) => {
    if (data.length > 0 || (props.comment.current_user_has_reaction && props.comment.current_user_type_reaction === type)) {
        defaultTabIndex.value = defaultTabIndex.value + 1
        defaultTabIndexObject.value[type] = defaultTabIndex.value
    }
}

const typeUserReactionsComment = (type) => {
    let data;
    switch (type) {
        case "like":
            data = props.comment.like_user_reactions
            break;
        case "love":
            data = props.comment.love_user_reactions
            break;
        case "care":
            data = props.comment.care_user_reactions
            break;
        case "haha":
            data = props.comment.haha_user_reactions
            break;
        case "wow":
            data = props.comment.wow_user_reactions
            break;
        case "sad":
            data = props.comment.sad_user_reactions
            break;
        case "angry":
            data = props.comment.angry_user_reactions
            break;
        default:
            break;
    }
    setDefaultTabIndex(data, type)
}

const emit = defineEmits(['callOpenAttachmentsModal', 'callOpenUserReactionsModal', 'callRestartGeneralDataFromPostComments', 'callRestartPostCommentList', 'callConfirmDeletion', 'callActiveShowNotification', 'callOpenDetailModal',])

const openUserReactionsModalToCommentList = (tabIndex) => {
    emit("callOpenUserReactionsModal", props.comment, tabIndex);
};

const confirmDeletion = (comment, entityPrefix) => {
    emit("callConfirmDeletion", comment, entityPrefix);
};

const activeShowNotification = (errors) => {
    emit("callActiveShowNotification", errors);
};

const openAttachmentPreview = (comment, index, entityPrefix) => {
    emit("callOpenAttachmentsModal", comment, index, entityPrefix);
};

const openUserReactionsModal = (comment, tabIndex) => {
    emit("callOpenUserReactionsModal", comment, tabIndex);
};

const restartGeneralDataFromPostComments = (generalData) => {
    props.comment.all_child_comments = generalData.all_child_comments

    emit('callRestartGeneralDataFromPostComments', generalData)
}

const restartPostCommentList = (latestComments, allComments) => {
    emit('callRestartPostCommentList', latestComments, allComments)
}

const commentHasResponses = () => {
    return props.comment.total_of_comments > 0 || props.comment.all_child_comments.length > 0
}

// ========================================================================================

// const attachmentFiles = ref([]);
const attachmentFilesComputed = computed(() => {
    // return [...attachmentFiles.value, ...(props.comment.attachments || [])];
    // return [...(props.comment.attachments || [])];
    return [...(props.comment.attachments || [])];
});

const maxFileNameLength = 15;
const printFileName = (fileName) => {
    if (fileName.length > maxFileNameLength) {
        return fileName.substring(0, maxFileNameLength) + "...";
    }
    return fileName;
};

const inputToEditRef = ref(null)
const cancelBlock = ref('ESC para cancelar')
const editingItem = ref(null)
const commentEditRef = ref(null)
const startEditingItem = (comment) => {
    // let commentTxt = props.comment.comment.length > 0
    //     ? props.comment.comment
    //     : 'NULO'

    // console.log('Editando COMMENT seleccionado...')
    // console.log('ID:', props.comment.id, '| TXT:', commentTxt)

    // --------------------------------------------------------------------

    // console.log('COMMENT llegado a startEditingItem', comment)
    // editingItem.value = comment
    // Nueva instancia ... solo lo necesario
    editingItem.value = {
        id: comment.id,
        comment: comment.comment,
        attachments: comment.attachments,
    }
    // console.log('EDITING_ITEM tras serle asignado COMMENT', editingItem.value)

    // if (inputToEditRef.value) {
    //     inputToEditRef.value.focus()
    // }
    // Así si
    // nextTick(() => inputToEditRef.value.focus());
    nextTick(() => {
        // inputToEditRef.value.addEventListener('keydown', cancelEditingItemOnEscape)
        // inputToEditRef.value.focus()

        commentEditRef.value.commentTextAreaRef.input.addEventListener('keydown', cancelEditingItemOnEscape)
        commentEditRef.value.commentTextAreaRef.focus()

        commentEditRef.value.reInitAdjustHeightTextArea()
    });
}

const cancelEditingItem = (isUpdated, dataUpdated) => {
    // inputToEditRef.value.removeEventListener('keydown', cancelEditingItemOnEscape)

    commentEditRef.value.commentTextAreaRef.input.removeEventListener('keydown', cancelEditingItemOnEscape)
    editingItem.value = null

    if (isUpdated) {
        props.comment.comment = dataUpdated.comment
        props.comment.attachments = dataUpdated.attachments
        props.comment.updated_at_large_format = dataUpdated.updated_at_large_format

        restartPostCommentList(dataUpdated.latest_comments, dataUpdated.all_comments)
    }
}

const cancelEditingItemOnEscape = (e) => {
    if (e.key === 'Escape' && editingItem.value) {
        cancelEditingItem(false, {});
    }
};


import ChildrenCommentBox from '@/Components/dearbook/Comment/Box.vue'
const childrenCommentBoxRef = ref(null)
const focusChildCommentTextArea = () => {
    nextTick(() => {
        if (childrenCommentBoxRef.value) {
            childrenCommentBoxRef.value.focusCommentTextAreaOfCreate()
        }
    })
};

const openDetailModal = () => {
    emit("callOpenDetailModal");
}

const limitingLatestCommentsListHigh = computed(
    () => props.typeList === 'latest' && !props.comment.parent_id && props.comment.total_of_direct_child_comments > 3
);
</script>

<template>
    <!-- <div v-if="editingItem">
        <p>Editando...</p>
        <input ref="inputToEditRef" type="text" @focusin="cancelBlock = 'ESC para cancelar'" @focusout="cancelBlock = 'cancelar'">
        <button @click="cancelEditingItem">
            {{ cancelBlock }}
        </button>
    </div> -->
    <CommentEdit v-if="editingItem && editingItem.id === comment.id" ref="commentEditRef" :comment-to-edit="editingItem"
        @callCancelEditingItem="cancelEditingItem" @callActiveShowNotification="activeShowNotification" />
    <!--  -->
    <div v-else class="flex gap-2 mt-2.5">
        <a :href="route('profile.index', { username: comment.user.username })" :title="'Perfil de ' + comment.user.name"
            class="h-fit">
            <div class="w-8 avatar offline">
                <img :src="comment.user.avatar_url ||
                    '/img/default_avatar.png'" class="transition-all border-2 rounded-full hover:border-cyan-500"
                    :alt="comment.user.name" />
            </div>
        </a>

        <div class="flex flex-col w-full group/block_comment">
            <div class="flex items-center gap-1">
                <div class="px-3 py-1 rounded-lg" :class="[
                    comment.comment.length > 0
                        ? 'bg-gray-200/50'
                        : ''
                ]">
                    <a :href="route('profile.index', { username: comment.user.username })"
                        class="text-[0.8125rem] font-semibold" :title="'Perfil de ' + comment.user.name">
                        {{ comment.user.name }}
                    </a>

                    <ReadMoreOrLess v-if="comment.comment.length > 0" :content="comment.comment"
                        :showing-banner-if-content-is-null="false" :max-content-length="maxCommentBodyLength"
                        :content-classes="'text-sm text-justify'" />
                </div>

                <OptionsDropDown v-model="isCommentAuthorAndEditable" :ellipsis-type-icon="'horizontal'"
                    :menu-button-classes="'opacity-0 group-hover/block_comment:opacity-100'"
                    :show-menu-item-icon="false">
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="startEditingItem(comment)" :class="[
                            active
                                ? 'bg-sky-100'
                                : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]" title="Editar comentario">
                            Editar
                        </button>
                        </MenuItem>
                    </div>

                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="confirmDeletion(comment, 'post.comment')" :class="[
                            active
                                ? 'bg-sky-100'
                                : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]" title="Eliminar">
                            Eliminar
                        </button>
                        </MenuItem>
                    </div>
                </OptionsDropDown>
            </div>

            <div v-if="attachmentFilesComputed.length > 0" class="mt-1.5">
                <CommentAttachments :comment="comment" :attachments="attachmentFilesComputed"
                    :max-file-name-length="maxFileNameLength" @callOpenAttachmentsModal="openAttachmentPreview"
                    @callPrintFileName="printFileName" />
            </div>

            <Disclosure v-if="typeList === 'all' || typeList === 'latest'">
                <div class="flex items-center gap-4 px-3 mt-0.5 text-xs text-gray-600">
                    <div class="z-10 tooltip tooltip-right" :data-tip="comment.created_at_large_format">
                        <small class="text-xs hover:cursor-pointer hover:underline">{{ comment.created_at_formatted
                            }}</small>
                    </div>

                    <CommentReactionBox v-if="!isTrashed && !isArchived" :comment="comment"
                        @callRestartDefaultTabIndex="setTypeUserReactionsCommentByType"
                        @callRestartPostCommentList="restartPostCommentList"
                        @callActiveShowNotification="activeShowNotification" />

                    <DisclosureButton v-if="!isTrashed && !isArchived" @click="focusChildCommentTextArea"
                        class="font-extrabold hover:underline">
                        Respuestas
                        <span v-if="commentHasResponses()">
                            ({{ comment.total_of_comments || comment.all_child_comments.length }})
                        </span>
                    </DisclosureButton>
                    <DisclosureButton v-else class="font-extrabold hover:underline">
                        Respuestas
                        <span v-if="commentHasResponses()">
                            ({{ comment.total_of_comments || comment.all_child_comments.length }})
                        </span>
                    </DisclosureButton>

                    <div v-if="comment.created_at != comment.updated_at" class="tooltip tooltip-top"
                        :data-tip="comment.updated_at_large_format">
                        <small class="text-xs italic hover:cursor-pointer hover:underline">Editado</small>
                    </div>

                    <div v-if="comment.total_of_reactions > 0" class="flex items-center">
                        <CommentReactionTypeUsersSummary :users-that-reacted="comment.all_user_reactions"
                            :current-user-has-reaction="comment.current_user_has_reaction
                                " :total-of-reactions="comment.total_of_reactions" :show-type-icon="false"
                            :show-header="false" @callOpenUserReactionsModal="openUserReactionsModalToCommentList(0)" />

                        <div class="flex items-center -space-x-0.5">
                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'like' ||
                                (comment.like_user_reactions &&
                                    comment.like_user_reactions.length > 0)
                            " :title="'Me gusta'" :type="'like'" :z-index-icon="'z-[7]'"
                                :users-that-reacted="comment.like_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['like'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'love' ||
                                (comment.love_user_reactions &&
                                    comment.love_user_reactions.length > 0)
                            " :title="'Me encanta'" :type="'love'" :z-index-icon="'z-[6]'"
                                :users-that-reacted="comment.love_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['love'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'care' ||
                                (comment.care_user_reactions &&
                                    comment.care_user_reactions.length > 0)
                            " :title="'Me importa'" :type="'care'" :z-index-icon="'z-[5]'"
                                :users-that-reacted="comment.care_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['care'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'haha' ||
                                (comment.haha_user_reactions &&
                                    comment.haha_user_reactions.length > 0)
                            " :title="'Me divierte'" :type="'haha'" :z-index-icon="'z-[4]'"
                                :users-that-reacted="comment.haha_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['haha'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'wow' ||
                                (comment.wow_user_reactions &&
                                    comment.wow_user_reactions.length > 0)
                            " :title="'Me asombra'" :type="'wow'" :z-index-icon="'z-[3]'"
                                :users-that-reacted="comment.wow_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['wow'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'sad' ||
                                (comment.sad_user_reactions &&
                                    comment.sad_user_reactions.length > 0)
                            " :title="'Me entristece'" :type="'sad'" :z-index-icon="'z-[2]'"
                                :users-that-reacted="comment.sad_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['sad'])" />

                            <CommentReactionTypeUsersSummary v-if="
                                comment.current_user_type_reaction === 'angry' ||
                                (comment.angry_user_reactions &&
                                    comment.angry_user_reactions.length > 0)
                            " :title="'Me enoja'" :type="'angry'" :z-index-icon="'z-[1]'"
                                :users-that-reacted="comment.angry_user_reactions" :current-user-type-reaction="comment.current_user_type_reaction
                                    "
                                @callOpenUserReactionsModal="openUserReactionsModalToCommentList(defaultTabIndexObject['angry'])" />
                        </div>
                    </div>
                </div>

                <!-- <transition enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-out"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0"> -->
                <transition enter-active-class="duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                    leave-active-class="duration-75 ease-out" leave-from-class="-translate-y-2 opacity-100"
                    leave-to-class="translate-y-0 opacity-0">
                    <DisclosurePanel :class="[
                        limitingLatestCommentsListHigh
                            ? 'children-comment-box max-h-[400px] overflow-auto'
                            : ''
                    ]">
                        <ChildrenCommentBox :is-trashed="isTrashed" :is-archived="isArchived" v-if="typeList === 'all'"
                            ref="childrenCommentBoxRef" :post="post" :comments-list="comment.all_child_comments"
                            :type-list="typeList" :create-action="'responding'" :parent-id="comment.id"
                            @callOpenAttachmentsModal="openAttachmentPreview"
                            @callOpenUserReactionsModal="openUserReactionsModal"
                            @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostComments"
                            @callRestartPostCommentList="restartPostCommentList" @callConfirmDeletion="confirmDeletion"
                            @callActiveShowNotification="activeShowNotification" />

                        <ChildrenCommentBox :is-trashed="isTrashed" :is-archived="isArchived"
                            v-else-if="typeList === 'latest'" ref="childrenCommentBoxRef" :post="post"
                            :comments-list="comment.latest_child_comments" :type-list="typeList"
                            :all-child-comments-total="comment.all_child_comments.length"
                            :show-more-comments-link="false" :create-action="'responding'" :parent-id="comment.id"
                            @callOpenAttachmentsModal="openAttachmentPreview"
                            @callOpenUserReactionsModal="openUserReactionsModal"
                            @callRestartGeneralDataFromPostComments="restartGeneralDataFromPostComments"
                            @callRestartPostCommentList="restartPostCommentList" @callConfirmDeletion="confirmDeletion"
                            @callActiveShowNotification="activeShowNotification"
                            @callOpenDetailModal="openDetailModal" />
                    </DisclosurePanel>
                </transition>
            </Disclosure>
        </div>
    </div>
</template>
