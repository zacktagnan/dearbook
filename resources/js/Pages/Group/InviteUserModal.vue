<script setup>
import BaseModal from '@/Components/dearbook/BaseModal.vue'
import CloseModal from '@/Components/dearbook/CloseModal.vue'
import TextInput from '@/Components/TextInput.vue'
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
import { ref, computed } from "vue";

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
    modelValue: Boolean,
});

const inviteForm = useForm({
    username_or_email: "",
});

const modalData = ref({
    dialogTitleText: "Invitar usuario",
    submitButtonText: "Invitar",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits(["update:modelValue", "callActiveShowNotification"]);

const closeModal = () => {
    show.value = false;

    inviteForm.reset()
    inviteForm.errors = []
};

const submitInvitation = () => {
    inviteForm.post(route("group.invite-users", props.group.slug), {
        preserveScroll: true,
        // onSuccess: (res) => {
        // Recibir esta RESpuesta (todo el objeto que es renderizado en la vista) no es relevante, al menos, por el momento
        onSuccess: () => {
            closeModal()
            emit('callActiveShowNotification')
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>
    <BaseModal v-model="show" :z-index="'z-30'" :dialog-panel-extra-classes="'max-w-lg'" @callCloseModal="closeModal">
        <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300 dark:border-gray-700">
            <div class="w-full text-center">
                <DialogTitle as="h3" class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ modalData.dialogTitleText }}
                </DialogTitle>
            </div>

            <CloseModal @call-close-modal="closeModal" />
        </div>

        <div class="flex px-[14px] mt-5 gap-3">
            <TextInput type="text" class="block w-full"
                :class="inviteForm.errors.username_or_email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                v-model="inviteForm.username_or_email" required autofocus
                :placeholder="$t('dearbook.group.form.invite_user.fields.username_or_email.placeholder')" />
        </div>

        <div class="px-[14px]">
            <InputError class="mt-2" :message="inviteForm.errors.username_or_email" />
            <!-- <InputError class="mt-2" :message="errorsForm.username_or_email" /> -->
        </div>

        <div class="flex p-[14px] gap-3">
            <button type="button" :title="$t('Cancel')"
                class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-sm text-gray-700 dark:text-gray-300 tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                @click="closeModal">
                {{ $t('Cancel') }}
            </button>

            <button type="button" :title="modalData.submitButtonText"
                class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-indigo-100 border border-transparent rounded-md text-sm text-indigo-900 tracking-widest shadow-sm hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 transition ease-in-out duration-150"
                :class="{ 'opacity-25': inviteForm.processing }"
                :disabled="inviteForm.processing" @click="submitInvitation">
                {{ modalData.submitButtonText }}
            </button>
        </div>
    </BaseModal>
</template>
