<script setup>
import BaseModal from '@/Components/dearbook/BaseModal.vue'
import CloseModal from '@/Components/dearbook/CloseModal.vue'
import {
    DialogTitle,
} from "@headlessui/vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
    members: Array,
    authUser: Object,
    modelValue: Boolean,
})

// Considerar el CREATOR como otro al que transferir la propiedad siempre que siga siendo Miembro del Group y ADMIN...
// -> para ello, como no está incluido dentro de MEMBERS, deberá ser recibido explícitamente igual que su comprobación de ser MEMBER y ADMIN.

// Tras la transferencia de la propiedad, se debe actualizar el listado principal de miembros satisfactoriamente.
// Ahora, sin esa actualización correcta, aparecen líneas de miembros duplicadas.
// Puede que sea necesario aplicar algo relativo a la actualización similar a cuando se elimina un miembro o en procesos similares.

console.log('group.creator', props.group.creator)
console.log('group.creator.role:', props.group.creator.role)

const adminMembersCollection = computed(() => {
    // return props.members.filter(member => member.role === 'admin' && member.id !== props.authUser.id)
    const filteredMembers = props.members.filter(member => member.role === 'admin' && member.id !== props.authUser.id)
    const creatorMember = props.group.creator.role === 'admin' && props.group.creator.id !== props.authUser.id ? props.group.creator : null
    return creatorMember ? [creatorMember, ...filteredMembers] : filteredMembers
})
console.log('adminMembersCollection-TRANSFER', adminMembersCollection.value)

const transferForm = useForm({
    user_id: "",
});

const modalData = ref({
    dialogTitleText: "Transferir propiedad",
    submitButtonText: "Transferir",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits(["update:modelValue", "callActiveShowNotification"]);

const closeModal = () => {
    show.value = false;

    transferForm.reset()
    transferForm.errors = []
};

const submitTransfer = () => {
    transferForm.post(route("group.transfer-ownership", props.group.slug), {
        preserveScroll: true,
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
    <BaseModal v-model="show" :z-index="'z-50'" :dialog-panel-extra-classes="'max-w-lg'" @callCloseModal="closeModal">
        <div class="flex items-center justify-between px-3 py-2 border border-b-gray-300 dark:border-gray-700">
            <div class="w-full text-center">
                <DialogTitle as="h3" class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ modalData.dialogTitleText }}
                </DialogTitle>
            </div>

            <CloseModal @call-close-modal="closeModal" />
        </div>

        <template v-if="adminMembersCollection.length === 0">
            <div class="my-5">
                <p class="px-6 py-2">No hay un ADMIN al que transferir la propiedad del grupo.</p>
                <p class="px-6 py-2">Asignar ese rol al miembro que vaya ser el nuevo propietario.</p>
            </div>
        </template>
        <template v-else>
            <div class="flex flex-col px-[14px] mt-5 gap-2">
                <p>Asignar la propiedad a uno de los ADMIN del grupo:</p>

                <div class="mx-auto">
                    <select v-model="transferForm.user_id"
                        class="rounded-md border-0 py-1 text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400 disabled:dark:text-gray-500 dark:bg-gray-800 hover:bg-sky-100 dark:hover:bg-slate-700">
                        <!-- <optgroup label="ADMINs">
                            <option value="0" selected disabled>Seleccionar entre los disponibles</option>
                        </optgroup> -->
                        <option value="0" selected disabled>Seleccionar entre los disponibles</option>
                        <option v-for="adminMember of adminMembersCollection" :value="adminMember.id">{{
                            `${adminMember.name} (${adminMember.username})` }}</option>
                    </select>
                </div>
            </div>

            <div class="flex p-[14px] gap-3">
                <button type="button" :title="$t('Cancel')"
                    class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-sm text-gray-700 dark:text-gray-300 tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                    @click="closeModal">
                    {{ $t('Cancel') }}
                </button>

                <button type="button" :title="modalData.submitButtonText"
                    class="w-1/2 inline-flex justify-center px-4 py-2 font-medium bg-indigo-100 border border-transparent rounded-md text-sm text-indigo-900 tracking-widest shadow-sm hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 transition ease-in-out duration-150"
                    :class="{ 'opacity-25': transferForm.processing }" :disabled="transferForm.processing"
                    @click="submitTransfer">
                    {{ modalData.submitButtonText }}
                </button>
            </div>
        </template>
    </BaseModal>
</template>
