<script setup>
import {
    PaperAirplaneIcon,
    PaperClipIcon,
    XMarkIcon,
    ArrowUturnLeftIcon,
} from "@heroicons/vue/24/solid";
import TextareaInput from '@/Components/TextareaInput.vue';
import { ref, watch, computed, onMounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { isImage, isVideo } from "@/Libs/helpers";
import InputError from "@/Components/InputError.vue";

import axiosClient from '@/axiosClient'

const props = defineProps({
    commentToEdit: {
        type: Object,
        required: true,
    },
})

const commentTextAreaRef = ref(null);

const commentToEditText = ref('')

onMounted(() => {
    commentToEditText.value = props.commentToEdit.comment || "";
})

const cancelFromWithin = ref(true)

const onFocusIn = () => {
    cancelFromWithin.value = true
}

const onFocusOut = () => {
    cancelFromWithin.value = false
}

const emit = defineEmits([
    'callCancelEditingItem',
    'callActiveShowNotification',
]);

/**
 * {
 *      file: File,
 *      url: '',
 * }
 */
const attachmentFiles = ref([]);
const attachmentFilesComputed = computed(() => {
    return [...attachmentFiles.value, ...(props.commentToEdit.attachments || [])];
});

watch(
    () => commentToEditText.value || attachmentFilesComputed.value.length,
    () => {
        // console.log("WATCH- Hubo cambio(s)...");
        disableSendCommentButton()
    }
)

const disableState = ref(true)
let disableStateTitleDefaultValue = 'Actualizar el contenido'
const disableStateTitle = ref(disableStateTitleDefaultValue)
const disableSendCommentButton = () => {
    disableState.value = commentToEditText.value.length <= 0 && attachmentFiles.value.length === 0

    disableStateTitle.value = disableState.value
        ? disableStateTitleDefaultValue
        : 'Aceptar cambios'
};

// =======================================================================================

const attachmentErrors = ref([]);

const allowedMimeTypes = usePage().props.allowedMimeTypes.join(", ") + ".";

const deleted_file_ids = ref([]);

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

const totalAttachment = computed(() => {
    return props.commentToEdit.attachments.length +
        attachmentFiles.value.length -
        deleted_file_ids.value.length
})

const uploadAttachmentSelected = async (event) => {
    for (const file of event.target.files) {
        const myFile = {
            file,
            url: await readFile(file),
        };
        attachmentFiles.value.push(myFile);
    }
    event.target.value = null;
};

const readFile = async (file) => {
    return new Promise((res, rej) => {
        if (isImage(file) || isVideo(file)) {
            const reader = new FileReader();

            reader.onload = () => {
                res(reader.result);
            };
            reader.onerror = rej;

            reader.readAsDataURL(file);
        }
        else {
            res(null);
        }
    });
};

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
    } else {
        deleted_file_ids.value.push(myFile.id);
        myFile.deleted = true;
    }
};

const revertDeleteMode = (myFile) => {
    deleted_file_ids.value = deleted_file_ids.value.filter(
        (id) => myFile.id !== id
    );
    myFile.deleted = false;

    if (attachmentFiles.value.length > 0) {
        attachmentFiles.value = []
    }
};

const cancellingEditingItem = (isUpdated, dataUpdated) => {
    if (commentTextAreaRef.value) {
        commentToEditText.value = '';
    }
    attachmentFiles.value = [];
    if (props.commentToEdit.attachments) {
        props.commentToEdit.attachments.forEach((file) => (file.deleted = false));
        deleted_file_ids.value = [];
    }
    attachmentErrors.value = [];
    totalAttachment.value = 0;
    deleted_file_ids.value = []

    emit('callCancelEditingItem', isUpdated, dataUpdated)
}

const reInitAdjustHeightTextArea = () => {
    if (commentTextAreaRef.value) {
        commentTextAreaRef.value.reInitAdjustHeight();
    }
};

const storingTheError = (index, errorMsg) => {
    attachmentErrors.value[index] = errorMsg;
}

const updateCommentData = () => {
    let attachments = attachmentFiles.value.map((myFile) => myFile.file);

    axiosClient.post(route('post.comment.update', props.commentToEdit.id), {
        comment: commentToEditText.value,
        attachments: attachments,
        deleted_file_ids: deleted_file_ids.value,
        _method: "PUT",
    }, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(({ data }) => {
            let dataUpdated = {
                // comment: data.comment,
                // attachments: data.attachments,
                // updated_at_large_format: data.updated_at_large_format,
                comment: data.commentUpdated.comment,
                attachments: data.commentUpdated.attachments,
                updated_at_large_format: data.commentUpdated.updated_at_large_format,

                latest_comments: data.latest_comments,
                all_comments: data.all_comments,
            }

            cancellingEditingItem(true, dataUpdated)
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
                storingTheError(index, errors[key][0])
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

defineExpose({
    reInitAdjustHeightTextArea, commentTextAreaRef,
})
</script>

<template>
    <div class="flex gap-2 mt-2.5">
        <a :href="route('profile.index', { username: $page.props.auth.user.username })"
            :title="'Perfil de ' + $page.props.auth.user.name" class="h-fit">
            <img :src="$page.props.auth.user.avatar_url"
                class="w-8 transition-all border-2 rounded-full hover:border-cyan-500"
                :alt="$page.props.auth.user.name" />
        </a>

        <div class="flex flex-col w-full gap-0">
            <TextareaInput ref="commentTextAreaRef" v-model="commentToEditText"
                :placeholder="'Comentar como ' + $page.props.auth.user.name"
                :class="'w-full rounded-none rounded-t-lg border-0 bg-gray-200/50 text-[15px] ring-0 focus:ring-0 resize-none pt-2 max-h-28'"
                rows="1" @callFocusIn="onFocusIn" @callFocusOut="onFocusOut"
                :block-item-class-name="'edit_block_item'" />

            <div class="flex items-center justify-between px-3 pb-2 rounded-none rounded-b-lg bg-gray-200/50 dark:bg-slate-900">
                <div class="mt-1.5">
                    <button type="button" class="relative group/ico_attach_multimedia_for_comment">
                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]">
                            <path
                                d="M432,112V96a48.14,48.14,0,0,0-48-48H64A48.14,48.14,0,0,0,16,96V352a48.14,48.14,0,0,0,48,48H80"
                                class="transition-colors duration-150 fill-none" :class="[
                                    totalAttachment === 0
                                        ? 'stroke-sky-300 group-hover/ico_attach_multimedia_for_comment:stroke-sky-500'
                                        : 'stroke-gray-400'
                                ]" style="
                                    stroke-linejoin: round;
                                    stroke-width: 32px;
                                "></path>
                            <rect x="96" y="128" width="400" height="336" rx="45.99" ry="45.99"
                                class="transition-colors duration-150 fill-none" :class="[
                                    totalAttachment === 0
                                        ? 'stroke-sky-300 group-hover/ico_attach_multimedia_for_comment:stroke-sky-500'
                                        : 'stroke-gray-400'
                                ]" style="
                                    stroke-linejoin: round;
                                    stroke-width: 32px;
                                "></rect>
                            <ellipse cx="372.92" cy="219.64" rx="30.77" ry="30.55"
                                class="transition-colors duration-150 fill-none" :class="[
                                    totalAttachment === 0
                                        ? 'stroke-sky-300 group-hover/ico_attach_multimedia_for_comment:stroke-sky-500'
                                        : 'stroke-gray-400'
                                ]" style="
                                    stroke-miterlimit: 10;
                                    stroke-width: 32px;
                                "></ellipse>
                            <path d="M342.15,372.17,255,285.78a30.93,30.93,0,0,0-42.18-1.21L96,387.64"
                                class="transition-colors duration-150 fill-none" :class="[
                                    totalAttachment === 0
                                        ? 'stroke-sky-300 group-hover/ico_attach_multimedia_for_comment:stroke-sky-500'
                                        : 'stroke-gray-400'
                                ]" style="
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-width: 32px;
                                "></path>
                            <path d="M265.23,464,383.82,346.27a31,31,0,0,1,41.46-1.87L496,402.91"
                                class="transition-colors duration-150 fill-none" :class="[
                                    totalAttachment === 0
                                        ? 'stroke-sky-300 group-hover/ico_attach_multimedia_for_comment:stroke-sky-500'
                                        : 'stroke-gray-400'
                                ]" style="
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-width: 32px;
                                "></path>
                        </svg>
                        <input @change="uploadAttachmentSelected
                            " :disabled="totalAttachment > 0" type="file"
                            class="absolute inset-0 opacity-0 cursor-pointer disabled:cursor-not-allowed" :title="totalAttachment === 0
                                ? 'Foto/video/otros'
                                : 'Solo un archivo es admitido'
                                " />
                    </button>
                </div>

                <button @click="updateCommentData" :disabled="disableState" :title="disableStateTitle"
                    class="text-cyan-700 hover:text-cyan-500 disabled:text-gray-400 disabled:cursor-not-allowed">
                    <PaperAirplaneIcon class="w-[18px] h-[18px]" />
                </button>
            </div>

            <template v-if="cancelFromWithin">
                <p class="text-xs">Pulsar ESC para <button @click="cancellingEditingItem(false, {})"
                        class="text-sky-500 hover:underline edit_block_item">cancelar</button></p>
            </template>
            <template v-else>
                <p class="text-xs"><button @click="cancellingEditingItem(false, {})"
                        class="text-sky-500 hover:underline">Cancelar</button></p>
            </template>

            <div v-if="showWarningExtensions" class="mx-3 mt-2">
                <div class="px-3 py-2 leading-4 border-l-4 border-amber-500 bg-amber-200">
                    <span class="font-bold text-amber-600">Extensiones permitidas:</span><br />
                    <small>{{ allowedMimeTypes }}</small>
                </div>
            </div>

            <div v-if="attachmentFilesComputed.length > 0" class="flex gap-4 mx-3 mt-2 w-fit">
                <template v-for="(
                        myFile, index
                    ) of attachmentFilesComputed">
                    <div class="flex flex-col">
                        <div class="flex gap-2">
                            <div class="relative text-gray-500 border w-fit" :class="attachmentErrors[index]
                                ? 'border-red-400'
                                : ''
                                ">
                                <!-- =============================================== -->
                                <div v-if="myFile.deleted"
                                    class="absolute inset-x-0 bottom-0 z-[9] flex items-center justify-between px-2 py-1 text-red-700 dark:text-red-900 bg-red-400/75">
                                    <span class="text-sm">to delete...</span>
                                </div>
                                <!-- =============================================== -->

                                <template v-if="
                                    isImage(
                                        myFile.file ||
                                        myFile
                                    ) ||
                                    isVideo(
                                        myFile.file ||
                                        myFile
                                    )
                                ">
                                    <img v-if="
                                        isImage(
                                            myFile.file ||
                                            myFile
                                        )
                                    " :src="myFile.url" :alt="(
                                        myFile.file ||
                                        myFile
                                    ).name
                                        " :title="(
                                            myFile.file ||
                                            myFile
                                        ).name
                                            " class="h-20" :class="[
                                                    myFile.deleted
                                                        ? 'opacity-50'
                                                        : '',
                                                ]" />
                                    <video v-if="
                                        isVideo(
                                            myFile.file ||
                                            myFile
                                        )
                                    " :src="myFile.url" controls :alt="(
                                        myFile.file ||
                                        myFile
                                    ).name
                                        " :title="(
                                            myFile.file ||
                                            myFile
                                        ).name
                                            " class="h-20" :class="[
                                                    myFile.deleted
                                                        ? 'opacity-50'
                                                        : '',
                                                ]"></video>
                                </template>

                                <template v-else>
                                    <div class="flex flex-col items-center justify-center p-1 px-1 bg-cyan-100" :class="[
                                        myFile.deleted
                                            ? 'opacity-50'
                                            : '',
                                    ]">
                                        <PaperClipIcon class="w-9 h-9 lg:w-11 lg:h-11" />

                                        <span class="text-xs text-center lg:text-sm" :title="[
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
                                        ]">
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


                            <button v-if="myFile.deleted" @click="
                                revertDeleteMode(
                                    myFile
                                )
                                " title="Revertir borrado"
                                class="flex items-center justify-center w-6 h-6 text-red-700 dark:text-red-900 bg-red-400 hover:text-white hover:bg-red-700 dark:hover:text-white dark:hover:bg-red-700 transition-all rounded-full cursor-pointer">
                                <ArrowUturnLeftIcon class="w-4 h-4" />
                            </button>
                            <button v-else @click="
                                removeFile(
                                    myFile,
                                    index
                                )
                                " title="Excluir"
                                class="flex items-center justify-center w-6 h-6 text-gray-100 bg-gray-300 hover:bg-gray-400 dark:text-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 transition-all rounded-full cursor-pointer">
                                <XMarkIcon class="w-4 h-4" />
                            </button>
                        </div>

                        <InputError :message="attachmentErrors[index]
                            " class="mt-0.5 *:text-xs" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
