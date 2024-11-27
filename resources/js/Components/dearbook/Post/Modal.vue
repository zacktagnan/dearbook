<script setup>
import PostHeader from "@/Components/dearbook/Post/Header.vue";
import UrlPreview from "@/Components/dearbook/Post/UrlPreview.vue";
// import TextareaInput from '@/Components/TextareaInput.vue';
import {
    XMarkIcon,
    PaperClipIcon,
    ArrowUturnLeftIcon,
    SparklesIcon,
} from "@heroicons/vue/24/solid";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed } from "vue";

import { isImage, isVideo } from "@/Libs/helpers";
import { editor, editorConfig } from "@/Libs/ckeditor-custom-config";

const onEditorReady = (editor) => {
    // Situando el cursor al final del contenido existente...
    editor.model.change((writer) => {
        writer.setSelection(editor.model.document.getRoot(), "end");
    });
    // Poniendo el foco en el editor...
    editor.editing.view.focus();
};

// La URL empleada para construir el previo
let previewUrl = ref(null)

const isTherePreviewData = ref(false)

const updateIsTherePreviewData = () => {
    isTherePreviewData.value = !!(postForm.preview.title || postForm.preview.image || postForm.preview.description)
}

const fetchPreview = (url) => {
    if (url === previewUrl.value) {
        return
    }

    previewUrl.value = url
    postForm.preview = {}

    if (url) {
        // console.log('Enviando petición...', url)
        axiosClient.post(route('post.fetch-url-preview'), { url })
            .then(({data}) => {
                console.log(data)
                postForm.preview = {
                    title: data['og:title'],
                    description: data['og:description'],
                    image: data['og:image'],
                    url,
                }
            })
            .catch(err => {
                console.log('ERRORES: ', err)
            })
            .finally(() => {
                updateIsTherePreviewData()
            })
    } else {
        removePreview()
    }
}

const removePreview = () => {
    postForm.preview = {}
    updateIsTherePreviewData()
}

const onInputChange = () => {
    let url = matchHref()
    console.log('URL tras matchHref', url)
    if (!url) {
        url = matchLink()
    }
    console.log('URL tras matchLink', url)
    // if (url) {
    //     fetchPreview(url)
    // }
    // else {
    //     previewUrl.value = null
    //     removePreview()
    // }
    // --------------------------------
    fetchPreview(url)
    // if (url) {
    //     fetchPreview(url)
    // }
}
const matchLink = () => {
    const urlRegex = /(?:https?):\/\/[^\s<]+/
    const match = postForm.body.match(urlRegex)

    if (match && match.length > 0) {
        console.log('matchLink: ', match[0])
        return match[0]
    }
    return null
}
const matchHref = () => {
    const urlRegex = /<a.+href="((https?):\/\/[^"]+)"/
    const match = postForm.body.match(urlRegex)

    if (match && match.length > 0) {
        console.log('matchHref: ', match[1])
        return match[1]
    }
    return null
}

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    group: {
        type: Object,
        default: null,
    },
    modelValue: Boolean,
});

const postForm = useForm({
    // id: null,
    body: "",
    preview: {},
    group_id: null,
    attachments: [],
    deleted_file_ids: [],
    _method: "POST",
});

const currentPostBodyOnDB = ref('')
const currentPostPreviewOnDB = ref({})
const processWellDone = ref(false)

const modalData = ref({
    dialogTitleText: "Crear publicación",
    submitButtonText: "Publicar",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits(["update:modelValue", "callActiveShowNotification"]);

watch(
    () => props.post,
    (newPost) => {
        if (newPost.id) {
            console.log("POST has changed...");

            postForm.body = newPost.body || "";
            currentPostBodyOnDB.value = newPost.body || "";

            postForm.preview = newPost.preview || {}
            currentPostPreviewOnDB.value = newPost.preview || {}

            updateIsTherePreviewData()
            // console.log('isTherePreviewData ACTUAL', isTherePreviewData.value)

            modalData.value.dialogTitleText = "Editar publicación";
            modalData.value.submitButtonText = "Actualizar";
            // onInputChange()
        } else {
            modalData.value.dialogTitleText = "Crear publicación"
            modalData.value.ubmitButtonText = "Publicar"
        }
    },
    {
        inmediate: true,
        deep: true,
    }
);

const closeModal = () => {
    show.value = false;
    attachmentFiles.value = [];

    currentPostBodyOnDB.value = props.post.body;
    currentPostPreviewOnDB.value = props.post.preview;

    // ---------------------------------------------------------------------
    // postForm.reset()
    // props.post.attachments.forEach(file => file.deleted = false)
    // ---------------------------------------------------------------------
    if (!props.post.id) {
        postForm.reset();
    } else {
        if (!processWellDone.value) {
            postForm.body = currentPostBodyOnDB.value
            postForm.preview = currentPostPreviewOnDB.value
        }
    }
    processWellDone.value = false

    if (props.post.attachments) {
        props.post.attachments.forEach((file) => (file.deleted = false));
        postForm.deleted_file_ids = [];
    }
    attachmentErrors.value = [];
    // showWarningExtensions.value = false;

    // Esto no porque, sino, al volver a reabrir el mismo Post justo después de cerrarlo,
    // ya no existirá el dato localmente, en el Frontend (a pesar de que siga existiendo en la BD)
    // y, por tanto, no se mostrará la vista previa
    // postForm.preview = {};
    // Esto si es necesario para que, justo después de cerrarse tras una actualización o no,
    // al reabrir el Modal, se siga mostrando el PREVIEW.
    // Si reinicio, facilita la generación y/o recarga del PREVIEW
    previewUrl.value = null;
};

import InputError from "@/Components/InputError.vue";
const attachmentErrors = ref([]);

const submitPost = () => {
    postForm.attachments = attachmentFiles.value.map((myFile) => myFile.file);

    if (props.post.id) {
        postForm.group_id = props.post.group ? props.post.group.id : null
        processUpdate();
    } else {
        postForm.group_id = props.group ? props.group.id : null
        processStore();
    }
};

const buildErrors = (key, errorMsg) => {
    let errors
    switch (key) {
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

const processStore = () => {
    postForm.post(route("post.store"), {
        preserveScroll: true,
        onSuccess: () => {
            processWellDone.value = true
            closeModal();
        },
        onError: (errors) => {
            processErrors(errors);
        },
    });
};

const processUpdate = () => {
    postForm._method = "PUT";
    postForm.post(route("post.update", props.post), {
        preserveScroll: true,
        onSuccess: () => {
            processWellDone.value = true
            closeModal();
        },
        onError: (errors) => {
            processErrors(errors);
            postForm.preview = currentPostPreviewOnDB.value
            updateIsTherePreviewData()
        },
    });
};

const processErrors = (errors) => {
    for (const key in errors) {
        if (key.includes(".")) {
            // const [field, index] = key.split('.')
            // const [, index] = key.split('.')
            const [item, index] = key.split(".");
            if (item === 'attachments') {
                attachmentErrors.value[index] = errors[key];
            }
            emit("callActiveShowNotification", buildErrors(item, errors[key]));
        } else {
            emit("callActiveShowNotification", postForm.errors);
        }
    }
};

const allowedMimeTypes = usePage().props.allowedMimeTypes.join(", ") + ".";

/**
 * {
 *      file: File,
 *      url: '',
 * }
 */
// const attachmentFiles = ref([])
// -----------------------------------------------------------
// const attachmentFiles = computed(() => {
//     if (postForm.id) {
//         return [...props.post.attachments]
//     }
//     return []
// })
// -----------------------------------------------------------
const attachmentFiles = ref([]);
const attachmentFilesComputed = computed(() => {
    //// return [...attachmentFiles.value, ...props.post.attachments]
    // Así da ERROR si no existen los ATTACHMENTs si Post es null
    // ------------------------------------------------------------------
    // if (props.post.id) {
    //     return [...attachmentFiles.value, ...props.post.attachments]
    // }
    // return attachmentFiles.value
    // Así si
    // ------------------------------------------------------------------
    // o, mejor, así
    return [...attachmentFiles.value, ...(props.post.attachments || [])];
});

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

const uploadAttachmentSelected = async (event) => {
    for (const file of event.target.files) {
        // let parts = file.name.split(".");
        // // se coge la última parte porque pudiera ser un nombre tal que así "name.xxx.ext"
        // let ext = parts.pop().toLowerCase();
        // if (!allowedMimeTypes.includes(ext)) {
        //     showWarningExtensions.value = true;
        // }

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
        // else if (isVideo(file)) {
        //     const blobURL = URL.createObjectURL(file)

        //     blobURL.onload = () => {
        //         res(reader.result)
        //     }
        //     blobURL.onerror = rej

        //     reader.readAsDataURL(file)
        // }
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
            // attachmentErrors.value = attachmentErrors.value.filter(id => index !== id)
            attachmentErrors.value = [];
        }
    } else {
        postForm.deleted_file_ids.push(myFile.id);
        myFile.deleted = true;
    }
};

const revertDeleteMode = (myFile) => {
    postForm.deleted_file_ids = postForm.deleted_file_ids.filter(
        (id) => myFile.id !== id
    );
    myFile.deleted = false;
};

const inputAttachmentFileRef = ref(null)
const openInputAttachmentFile = () => {
    if (inputAttachmentFileRef.value) {
        inputAttachmentFileRef.value.click()
    }
}

import axiosClient from '@/axiosClient'
const generatingContent = ref(false)
const generateContent = () => {
    if (!postForm.body) {
        return
    }
    generatingContent.value = true

    axiosClient.post(route('post.generate-content'), {
        prompt: postForm.body,
    })
        .then(({ data }) => {
            console.log(data.content)
            postForm.body = data.content
        })
        .catch(err => {
            console.log(err)
        })
        .finally(() => {
            generatingContent.value = false
        })
}
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
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
                                class="w-full max-w-lg overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl">
                                <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300">
                                    <div class="w-full text-center">
                                        <DialogTitle as="h3" class="text-lg font-bold text-gray-900">
                                            {{ modalData.dialogTitleText }}
                                        </DialogTitle>
                                    </div>

                                    <button @click="closeModal"
                                        class="flex items-center p-1 font-bold text-gray-500 transition-colors duration-200 bg-gray-200 rounded-full hover:bg-gray-300 hover:text-gray-700"
                                        title="Cerrar">
                                        <XMarkIcon class="w-5 h-5" />
                                    </button>
                                </div>

                                <div class="overflow-auto" :class="[
                                    isTherePreviewData
                                    ? 'h-[414px] md:h-[505px]'
                                    : ''
                                ]">
                                    <div class="px-[14px] pt-[14px]">
                                        <PostHeader :post="post" :show-post-date="false" />

                                        <div v-if="postForm.errors.group_id"
                                            class="rounded-md bg-red-500 text-white px-3 py-2 mb-2 text-sm font-bold">
                                            {{ postForm.errors.group_id }}
                                        </div>

                                        <!-- <TextareaInput placeholder="Expresa lo que quieras comunicar" class="w-full mt-2"
                                            v-model="postForm.body" autofocus></TextareaInput> -->
                                        <!-- o -->
                                        <!-- <ckeditor :editor="editor" @ready="onEditorReady" v-model="postForm.body"
                                            :config="editorConfig">
                                        </ckeditor> -->
                                        <!-- o -->
                                        <div class="relative group">
                                            <ckeditor :editor="editor" @ready="onEditorReady" v-model="postForm.body"
                                                :config="editorConfig"
                                                @input="onInputChange">
                                            </ckeditor>

                                            <UrlPreview
                                                :is-there-preview-data="isTherePreviewData" :url="previewUrl"
                                                :preview="postForm.preview"
                                                :classes="'relative border border-cyan-200 rounded-lg mt-4 bg-sky-50'"
                                                @call-remove-preview="removePreview" />

                                            <button @click="generateContent" :disabled="generatingContent" class="absolute right-2 top-12 size-6 p-1 rounded bg-cyan-600 hover:bg-cyan-500 text-white transition-all opacity-0 group-hover:opacity-100 disabled:cursor-not-allowed disabled:bg-cyan-300 disabled:hover:bg-cyan-300 disabled:text-gray-600" title="Generar contenido...">
                                                <svg v-if="generatingContent" class="animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <SparklesIcon v-else />
                                            </button>
                                        </div>
                                    </div>

                                    <div v-if="showWarningExtensions" class="m-4">
                                        <div class="px-3 py-2 leading-4 border-l-4 border-amber-500 bg-amber-200">
                                            <span class="font-bold text-amber-600">Extensiones permitidas:</span><br />
                                            <small>{{ allowedMimeTypes }}</small>
                                        </div>
                                    </div>

                                    <div v-if="attachmentFilesComputed.length > 0" class="m-[14px]">
                                        <div class="grid gap-3 mt-1 overflow-auto h-60" :class="[
                                            attachmentFilesComputed.length === 1
                                                ? 'grid-cols-1'
                                                : 'grid-cols-2 lg:grid-cols-3',
                                        ]">
                                            <div v-for="(
                                                    myFile, index
                                                ) of attachmentFilesComputed">
                                                <div class="relative flex flex-col items-center justify-center text-gray-500 border-2 aspect-square bg-cyan-100 group"
                                                    :class="attachmentErrors[index]
                                                        ? 'border-red-400'
                                                        : ''
                                                        ">
                                                    <div v-if="myFile.deleted"
                                                        class="absolute inset-x-0 bottom-0 z-40 flex items-center justify-between px-2 py-1 text-red-700 bg-red-400/75">
                                                        <span class="text-sm">to delete...</span>

                                                        <button @click="
                                                            revertDeleteMode(
                                                                myFile
                                                            )
                                                            " title="Revertir borrado"
                                                            class="p-1 text-red-700 rounded-full cursor-pointer hover:bg-red-700 hover:text-white">
                                                            <ArrowUturnLeftIcon class="w-5 h-5" />
                                                        </button>
                                                    </div>

                                                    <button @click="
                                                        removeFile(
                                                            myFile,
                                                            index
                                                        )
                                                        " title="Excluir"
                                                        class="absolute flex items-center justify-center text-gray-100 transition-all bg-gray-300 rounded-full opacity-0 cursor-pointer w-7 h-7 group-hover:opacity-100 hover:bg-gray-400 right-2 top-2">
                                                        <XMarkIcon class="w-5 h-5" />
                                                    </button>

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
                                                                " class="object-contain w-10/12 aspect-square" :class="[
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
                                                                " class="object-contain w-10/12 aspect-square" :class="[
                                                                    myFile.deleted
                                                                        ? 'opacity-50'
                                                                        : '',
                                                                ]"></video>
                                                    </template>

                                                    <template v-else>
                                                        <div class="flex flex-col items-center justify-center px-1" :class="[
                                                            myFile.deleted
                                                                ? 'opacity-50'
                                                                : '',
                                                        ]">
                                                            <PaperClipIcon class="w-10 h-10 lg:w-12 lg:h-12" />

                                                            <span class="text-sm text-center lg:text-base" :title="[
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
                                                                <!-- {{ (myFile.file || myFile).name }} -->
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

                                                <!-- <div>
                                                    {{ attachmentErrors[index] }}
                                                </div> -->
                                                <InputError :message="attachmentErrors[index]
                                                    " class="mt-1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex p-[14px] mx-[14px] mt-[14px] border border-gray-300 rounded-md justify-between items-center">
                                    <span class="font-medium">Añadir a tu publicación</span>

                                    <div>
                                        <button @click="openInputAttachmentFile" type="button"
                                            class="relative group/ico_attach_multimedia">
                                            <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                                                class="w-6 h-6">
                                                <path
                                                    d="M432,112V96a48.14,48.14,0,0,0-48-48H64A48.14,48.14,0,0,0,16,96V352a48.14,48.14,0,0,0,48,48H80"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="
                                                        stroke-linejoin: round;
                                                        stroke-width: 32px;
                                                    "></path>
                                                <rect x="96" y="128" width="400" height="336" rx="45.99" ry="45.99"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="
                                                        stroke-linejoin: round;
                                                        stroke-width: 32px;
                                                    "></rect>
                                                <ellipse cx="372.92" cy="219.64" rx="30.77" ry="30.55"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="
                                                        stroke-miterlimit: 10;
                                                        stroke-width: 32px;
                                                    "></ellipse>
                                                <path
                                                    d="M342.15,372.17,255,285.78a30.93,30.93,0,0,0-42.18-1.21L96,387.64"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="
                                                        stroke-linecap: round;
                                                        stroke-linejoin: round;
                                                        stroke-width: 32px;
                                                    "></path>
                                                <path d="M265.23,464,383.82,346.27a31,31,0,0,1,41.46-1.87L496,402.91"
                                                    class="transition-colors duration-150 fill-none stroke-sky-300 group-hover/ico_attach_multimedia:stroke-sky-500"
                                                    style="
                                                        stroke-linecap: round;
                                                        stroke-linejoin: round;
                                                        stroke-width: 32px;
                                                    "></path>
                                            </svg>
                                            <input @change="uploadAttachmentSelected
                                                " type="file" ref="inputAttachmentFileRef" multiple
                                                class="absolute inset-0 opacity-0 cursor-pointer"
                                                title="Foto/video/otros" />
                                        </button>
                                    </div>
                                </div>

                                <div class="flex p-[14px]">
                                    <button type="button" :title="modalData.submitButtonText"
                                        class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-indigo-900 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                        @click="submitPost">
                                        {{ modalData.submitButtonText }}
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
