<script setup>
import BaseModal from '@/Components/dearbook/BaseModal.vue'
import CloseModal from '@/Components/dearbook/CloseModal.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import Radiobutton from '@/Components/Radiobutton.vue'
import TextareaInput from '@/Components/TextareaInput.vue'
import InputError from '@/Components/InputError.vue'
import { DialogTitle } from "@headlessui/vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

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
    auto_approval: false,
    type: "public",
    about: "",
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

const autoApprovalGroupRef = ref(null)
const autoApprovalDisabled = ref(false)

const autoApprovalToFalseAndDisable = (disable) => {
    // groupForm.auto_approval = disable
    autoApprovalDisabled.value = !disable
    if (autoApprovalDisabled.value) {
        groupForm.auto_approval = false
        // autoApprovalGroupRef.value.checked = false
    }
    console.log('================================================')
    console.log('groupForm.type', groupForm.type)
    console.log('autoApprovalDisabled', autoApprovalDisabled.value)
    console.log('groupForm.auto_approval', groupForm.auto_approval)
}

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
    errorsForm.value = {}
    autoApprovalDisabled.value = false
};

const submitGroup = () => {
    processStore()
}

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
}

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
    <BaseModal v-model="show" :z-index="'z-10'" :dialog-panel-extra-classes="'max-w-lg'" @callCloseModal="closeModal">
        <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300 dark:border-gray-700">
            <div class="w-full text-center">
                <DialogTitle as="h3" class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ modalData.dialogTitleText }}
                </DialogTitle>
            </div>

            <CloseModal @call-close-modal="closeModal" />
        </div>

        <div class="flex px-[14px] mt-5 gap-3">
            <TextInput type="text" class="block w-full" v-model="groupForm.name" required
                autofocus :placeholder="$t('dearbook.group.form.create.fields.name.placeholder')" />
        </div>

        <div class="px-[14px]">
            <!-- <InputError class="mt-2" :message="groupForm.errors.name" /> -->
            <InputError class="mt-2" :message="errorsForm.name" />
        </div>


        <div class="flex justify-center gap-11 px-[14px] mt-5">
            <label disabled class="flex items-center whitespace-nowrap">
                <Checkbox ref="autoApprovalGroupRef" class="disabled:bg-gray-100 dark:disabled:bg-gray-600"
                    :disabled="autoApprovalDisabled"
                    v-model:checked="groupForm.auto_approval" />
                <span :class="[
                    autoApprovalDisabled
                        ? 'text-gray-400 dark:text-gray-600'
                        : 'text-gray-600 dark:text-gray-400'
                ]" class="text-sm ms-2">{{
                    $t('dearbook.group.form.create.fields.auto_approval.label') }}</span>
            </label>

            <div class="flex gap-4">
                <label class="flex items-center">
                    <Radiobutton @change="autoApprovalToFalseAndDisable(true)"
                        v-model:checked="groupForm.type" :value="'public'" />
                    <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">{{
                        $t('dearbook.group.form.create.fields.type.label.public') }}</span>
                </label>

                <label class="flex items-center">
                    <Radiobutton @change="autoApprovalToFalseAndDisable(false)"
                        v-model:checked="groupForm.type" :value="'private'" />
                    <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">{{
                        $t('dearbook.group.form.create.fields.type.label.private') }}</span>
                </label>
            </div>
        </div>


        <div class="px-[14px] mt-5">
            <TextareaInput v-model="groupForm.about"
                :placeholder="$t('dearbook.group.form.create.fields.about.placeholder')"
                :class="'block w-full max-h-28'" rows="2" />

            <!-- <InputError class="mt-2" :message="groupForm.errors.about" /> -->
            <InputError class="mt-2" :message="errorsForm.about" />
        </div>

        <div class="flex p-[14px] gap-3">
            <button type="button" :title="$t('Cancel')"
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
    </BaseModal>
</template>
