<script setup>
import {
    EllipsisHorizontalIcon,
    PaperClipIcon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';
import CommentReactionBox from '@/Components/dearbook/Comment/Reaction/Box.vue'
import CommentReactionTypeUsersSummary from "@/Components/dearbook/Comment/Reaction/TypeUsersSummary.vue";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import axiosClient from "@/axiosClient";
import { isImage, isVideo, reactionTypesFormat, } from "@/Libs/helpers";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    comment: Object,
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

// const isCommentAuthor = () => authUser && authUser.id === props.comment.user.id
// Sin el COMPUTED, funciona y debe ser llamado como método >> isCommentAuthor()
// Con el COMPUTED, funciona como una simple variable computada y debe ser llamado como tal
const isCommentAuthor = computed(
    () => authUser && authUser.id === props.comment.user.id
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

const emit = defineEmits(['callActiveShowNotificationToLatestList', 'callOpenUserReactionsModalToLatestList', 'callOpenAttachmentsModalToLatestList',])

const openUserReactionsModalToLatestList = (tabIndex) => {
    emit("callOpenUserReactionsModalToLatestList", props.comment, tabIndex);
};

const activeShowNotificationToLatestList = (errors) => {
    emit("callActiveShowNotificationToLatestList", errors);
};

// ========================================================================================

/**
 * {
 *      file: File,
 *      url: '',
 * }
 */
const attachmentFiles = ref([]);
const attachmentFilesComputed = computed(() => {
    return [...attachmentFiles.value, ...(props.comment.attachments || [])];
});

const attachmentErrors = ref([]);

const allowedMimeTypes = usePage().props.allowedMimeTypes.join(", ") + ".";

const showWarningExtensions = computed(() => {
    for (let myFile of attachmentFiles.value) {
        const file = myFile.file
        let parts = file.name.split(".");
        // se coge la última parte porque pudiera ser un nombre tal que así "name.xxx.ext"
        let ext = parts.pop().toLowerCase();
        if (!allowedMimeTypes.includes(ext)) {
            return true;
        }
    }

    return false
})

const maxFileNameLength = 15;
const printFileName = (fileName) => {
    if (fileName.length > maxFileNameLength) {
        return fileName.substring(0, maxFileNameLength) + "...";
    }
    return fileName;
};

const removeFile = (myFile, index) => {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(
            (f) => f !== myFile
        );

        showWarningExtensions.value = false;

        if (attachmentErrors.value[index]) {
            attachmentErrors.value = [];
        }
    }
};

const openAttachmentPreview = (index) => {
    emit("callOpenAttachmentsModalToLatestList", props.comment, index, 'post.comment');
};
</script>

<template>
    <a :href="route('profile.index', { username: comment.user.username })"
        :title="'Perfil de ' + comment.user.name">
        <div class="w-8 avatar offline">
            <img :src="comment.user.avatar_url ||
            '/img/default_avatar.png'" class="transition-all border-2 rounded-full hover:border-cyan-500"
                :alt="comment.user.name" />
        </div>
    </a>

    <div class="flex flex-col w-full group/block_comment">
        <div class="flex items-center gap-1">
            <div
                class="px-3 py-1 rounded-lg"
                :class="[
                    comment.comment.length > 0
                    ? 'bg-gray-200/50'
                    : ''
                ]"
            >
                <a :href="route('profile.index', { username: comment.user.username })" class="text-[0.8125rem] font-semibold" :title="'Perfil de ' + comment.user.name">
                    {{ comment.user.name }}
                </a>

                <ReadMoreOrLess :content="comment.comment" :showing-banner-if-content-is-null="false"
                    :max-content-length="maxCommentBodyLength"
                    :content-classes="'text-sm text-justify'" />
            </div>

            <Menu
                v-if="isCommentAuthor"
                as="div"
                class="relative inline-block text-left"
            >
                <div>
                    <MenuButton
                        class="p-1 transition-colors duration-150 rounded-full opacity-0 hover:bg-black/5 group-hover/block_comment:opacity-100"
                        title="Ver opciones"
                    >
                        <EllipsisHorizontalIcon
                            class="w-5 h-5"
                            aria-hidden="true"
                        />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="absolute right-0 z-10 w-24 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black/5 focus:outline-none"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="openEditModal"
                                    :class="[
                                        active
                                            ? 'bg-indigo-100'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                    title="Edit"
                                >
                                    Edit
                                </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="deleteComment"
                                    :class="[
                                        active
                                            ? 'bg-indigo-100'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                    title="Eliminar"
                                >
                                    Eliminar
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>

        <div v-if="attachmentFilesComputed.length > 0" class="mt-1.5">
            <div v-for="(myFile, index) of attachmentFilesComputed" class="flex gap-4">
                <div class="flex flex-col">
                    <div class="flex gap-2">
                        <div
                            class="p-1 border w-fit rounded-2xl"
                            :class="
                                attachmentErrors[index]
                                    ? 'border-red-400'
                                    : 'border-gray-400'
                            "
                        >
                            <div
                                @click="openAttachmentPreview(index)"
                                title="Ver en detalle"
                                class="overflow-hidden cursor-pointer rounded-2xl"
                            >
                                <template
                                    v-if="
                                        isImage(
                                            myFile.file ||
                                                myFile
                                        ) ||
                                        isVideo(
                                            myFile.file ||
                                                myFile
                                        )
                                    "
                                >
                                    <img
                                        v-if="
                                            isImage(
                                                myFile.file ||
                                                    myFile
                                            )
                                        "
                                        :src="myFile.url"
                                        :alt="
                                            (
                                                myFile.file ||
                                                myFile
                                            ).name
                                        "
                                        class="object-fill max-w-60"
                                    />
                                    <video
                                        v-if="
                                            isVideo(
                                                myFile.file ||
                                                    myFile
                                            )
                                        "
                                        :src="myFile.url"
                                        controls
                                        :alt="
                                            (
                                                myFile.file ||
                                                myFile
                                            ).name
                                        "
                                        class="object-fill h-20"
                                    ></video>
                                </template>

                                <template v-else>
                                    <div
                                        class="flex flex-col items-center justify-center p-1 px-1 bg-cyan-100"
                                    >
                                        <PaperClipIcon
                                            class="w-9 h-9 lg:w-11 lg:h-11"
                                        />

                                        <span
                                            class="text-xs text-center lg:text-sm"
                                            :title="[
                                                (
                                                    myFile.file ||
                                                    myFile
                                                ).name.length >
                                                maxFileNameLength
                                                    ? (
                                                            myFile.file ||
                                                            myFile
                                                        ).name
                                                    : '',
                                            ]"
                                        >
                                            {{
                                                printFileName(
                                                    (
                                                        myFile.file ||
                                                        myFile
                                                    ).name
                                                )
                                            }}
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- <button
                            @click="
                                removeFile(
                                    myFile,
                                    index
                                )
                            "
                            title="Excluir"
                            class="flex items-center justify-center w-6 h-6 text-gray-100 transition-all bg-gray-300 rounded-full cursor-pointer hover:bg-gray-400"
                        >
                            <XMarkIcon
                                class="w-4 h-4"
                            />
                        </button> -->
                    </div>

                    <InputError
                        :message="
                            attachmentErrors[index]
                        "
                        class="mt-0.5 *:text-xs"
                    />
                </div>

                <!-- <div>otro adjunto</div> -->
            </div>
        </div>

        <div class="flex items-center gap-4 px-3 mt-0.5 text-xs text-gray-600">
            <div class="z-10 tooltip tooltip-right" :data-tip="comment.created_at_large_format">
                <small class="text-xs hover:cursor-pointer hover:underline">{{ comment.created_at_formatted }}</small>
            </div>

            <CommentReactionBox
                :comment="comment"
                @callRestartDefaultTabIndex="setTypeUserReactionsCommentByType"
                @callActiveShowNotificationToCommentItem="activeShowNotificationToLatestList"
            />

            <button class="font-extrabold hover:underline">Responder</button>

            <div v-if="comment.created_at != comment.updated_at" class="tooltip tooltip-top" :data-tip="comment.updated_at_large_format">
                <small class="text-xs italic hover:cursor-pointer hover:underline">Editado</small>
            </div>

            <div v-if="comment.total_of_reactions > 0" class="flex items-center">
                <CommentReactionTypeUsersSummary
                    :users-that-reacted="comment.all_user_reactions"
                    :current-user-has-reaction="
                        comment.current_user_has_reaction
                    "
                    :total-of-reactions="comment.total_of_reactions"
                    :show-type-icon="false"
                    :show-header="false"
                    @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(0)"
                />

                <div class="flex items-center -space-x-0.5">
                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'like' ||
                            (comment.like_user_reactions &&
                                comment.like_user_reactions.length > 0)
                        "
                        :title="'Me gusta'"
                        :type="'like'"
                        :z-index-icon="'z-[7]'"
                        :users-that-reacted="comment.like_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['like'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'love' ||
                            (comment.love_user_reactions &&
                                comment.love_user_reactions.length > 0)
                        "
                        :title="'Me encanta'"
                        :type="'love'"
                        :z-index-icon="'z-[6]'"
                        :users-that-reacted="comment.love_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['love'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'care' ||
                            (comment.care_user_reactions &&
                                comment.care_user_reactions.length > 0)
                        "
                        :title="'Me importa'"
                        :type="'care'"
                        :z-index-icon="'z-[5]'"
                        :users-that-reacted="comment.care_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['care'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'haha' ||
                            (comment.haha_user_reactions &&
                                comment.haha_user_reactions.length > 0)
                        "
                        :title="'Me divierte'"
                        :type="'haha'"
                        :z-index-icon="'z-[4]'"
                        :users-that-reacted="comment.haha_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['haha'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'wow' ||
                            (comment.wow_user_reactions &&
                                comment.wow_user_reactions.length > 0)
                        "
                        :title="'Me asombra'"
                        :type="'wow'"
                        :z-index-icon="'z-[3]'"
                        :users-that-reacted="comment.wow_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['wow'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'sad' ||
                            (comment.sad_user_reactions &&
                                comment.sad_user_reactions.length > 0)
                        "
                        :title="'Me entristece'"
                        :type="'sad'"
                        :z-index-icon="'z-[2]'"
                        :users-that-reacted="comment.sad_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['sad'])"
                    />

                    <CommentReactionTypeUsersSummary
                        v-if="
                            comment.current_user_type_reaction === 'angry' ||
                            (comment.angry_user_reactions &&
                                comment.angry_user_reactions.length > 0)
                        "
                        :title="'Me enoja'"
                        :type="'angry'"
                        :z-index-icon="'z-[1]'"
                        :users-that-reacted="comment.angry_user_reactions"
                        :current-user-type-reaction="
                            comment.current_user_type_reaction
                        "
                        @callOpenUserReactionsModalToCommentItem="openUserReactionsModalToLatestList(defaultTabIndexObject['angry'])"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
