<script setup>
// import TextareaInput from '@/Components/TextareaInput.vue';
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import Radiobutton from '@/Components/Radiobutton.vue'
import TextareaInput from '@/Components/TextareaInput.vue'
import InputError from '@/Components/InputError.vue'
import {
    XMarkIcon,
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

import axiosClient from '@/axiosClient'

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
    modelValue: Boolean,
});

const groupForm = useForm({
    name: "",
    auto_approval: true,
    type: "public",
    about: "",
    // _method: "POST",
});

const currentGroupNameOnDB = ref('')
const processWellDone = ref(false)

const modalData = ref({
    dialogTitleText: "Crear grupo",
    submitButtonText: "Crear",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits(["update:modelValue", "callGroupCreated", "callActiveShowNotification"]);

// watch(
//     () => props.group,
//     () => {
//         console.log("GROUP has changed...");
//         // groupForm.id = props.group.id
//         // ---------------------------------------------------------------------
//         // groupForm.name = props.group.name
//         groupForm.name = props.group.name || "";
//         currentGroupNameOnDB.value = props.group.name || "";

//         modalData.value.dialogTitleText = "Editar grupo";
//         modalData.value.submitButtonText = "Actualizar";
//         // ---------------------------------------------------------------------
//         // if (props.group) {
//         //     groupForm.name = props.group.name

//         //     modalData.value.dialogTitleText = 'Editar publicación'
//         //     modalData.value.submitButtonText = 'Actualizar'
//         // }
//     },
//     {
//         deep: true,
//     }
// );

const closeModal = () => {
    show.value = false;

    // ---------------------------------------------------------------------
    // groupForm.reset()
    // props.group.attachments.forEach(file => file.deleted = false)
    // ---------------------------------------------------------------------
    if (!props.group.id) {
        groupForm.reset();
    } else {
        if (!processWellDone.value) {
            groupForm.name = currentGroupNameOnDB.value
        }
    }
    processWellDone.value = false
    groupForm.errors = []
};

const submitGroup = () => {
    // groupForm.attachments = attachmentFiles.value.map((myFile) => myFile.file);

    if (props.group.id) {
        processUpdate();
    } else {
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
    // groupForm.post(route("group.store"), {
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         processWellDone.value = true
    //         closeModal();
    //     },
    //     onError: (errors) => {
    //         processErrors(errors);
    //     },
    // });
    // En este caso, se consigue más optimización de la petición y de lo que devuelve ésta
    // mediante el uso de AXIOS en vez de a través del ruteo de Inertia
    axiosClient.post(route('group.store'), groupForm)
        .then(({ data }) => {
            console.log(data)
            closeModal()
            emit('callGroupCreated', data)
        })
        // .catch((error) => {
        //     console.log('ERROR al crear un Grupo', error.response.data.errors)
        // })
        // Con AXIOS, los ERRORES de Form no vienen incluidos con el groupForm
        // Por eso, deben de tratarse por separado
        .catch((error) => {
            processErrors(error.response.data.errors)
        })
};

const processUpdate = () => {
    groupForm._method = "PUT";
    groupForm.post(route("group.update", props.group), {
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

const errorsForm = ref({})
const processErrors = (errors) => {
    for (const key in errors) {
        switch (key) {
            case 'name':
                errorsForm.value = {
                    name: errors[key][0]
                }
                break;
            case 'about':
                errorsForm.value = {
                    about: errors[key][0]
                }
                break;
            default:
                break;
        }
    }
};
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
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

                                <div class="flex px-[14px] mt-5 gap-3">
                                    <TextInput type="text" class="block w-full" v-model="groupForm.name" required
                                        autofocus :placeholder="$t('dearbook.group.create.fields.name.placeholder')" />
                                </div>

                                <div class="px-[14px]">
                                    <!-- <InputError class="mt-2" :message="groupForm.errors.name" /> -->
                                    <InputError class="mt-2" :message="errorsForm.name" />
                                </div>


                                <div class="flex justify-center gap-11 px-[14px] mt-5">
                                    <label class="flex items-center whitespace-nowrap">
                                        <Checkbox v-model:checked="groupForm.auto_approval" />
                                        <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">{{
                                            $t('dearbook.group.create.fields.auto_approval.label') }}</span>
                                    </label>

                                    <div class="flex gap-4">
                                        <label class="flex items-center">
                                            <Radiobutton v-model:checked="groupForm.type" :value="'public'" />
                                            <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">Público</span>
                                        </label>

                                        <label class="flex items-center">
                                            <Radiobutton v-model:checked="groupForm.type" :value="'private'" />
                                            <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">Privado</span>
                                        </label>
                                    </div>
                                </div>


                                <div class="px-[14px] mt-5">
                                    <TextareaInput ref="commentTextAreaRef" v-model="groupForm.about"
                                        :placeholder="$t('dearbook.group.create.fields.about.placeholder')"
                                        :class="'block w-full max-h-28'" rows="2" />

                                    <InputError class="mt-2" :message="groupForm.errors.about" />
                                </div>

                                <div class="flex p-[14px] gap-3">
                                    <button type="button" :title="modalData.submitButtonText"
                                        class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-sm text-gray-700 dark:text-gray-300 tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                        @click="closeModal">
                                        {{ $t('Cancel') }}
                                    </button>

                                    <button type="button" :title="modalData.submitButtonText"
                                        class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-indigo-100 border border-transparent rounded-md text-sm text-indigo-900 tracking-widest shadow-sm hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 transition ease-in-out duration-150"
                                        :class="{ 'opacity-25': groupForm.processing }" :disabled="groupForm.processing"
                                        @click="submitGroup">
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
