<script setup>
import GroupItem from '@/Pages/Profile/Partials/GroupInformation/Item.vue'
// - - - -
import ConfirmLeaveGroupModal from '@/Components/Modal.vue'

import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'

import { ref } from 'vue';
// - - - -

defineProps({
    groupsOwned: {
        type: Array,
    },
    groupsJoined: {
        type: Array,
    },
    // - - - -
    isMyProfile: {
        type: Boolean,
        default: false,
    },
    // - - - -
});

// - - - -
const groupToLeave = ref(null)

const showingConfirmLeaveGroupModal = ref(false)

const showConfirmLeaveGroupFromProfileModal = (group) => {
    groupToLeave.value = group
    showingConfirmLeaveGroupModal.value = true;
    console.log('groupToLeave', groupToLeave.value)
}
const closeConfirmLeaveGroupModal = () => {
    showingConfirmLeaveGroupModal.value = false;
}
// - - - -
</script>

<template>
    <div
        class="pt-2 pl-2 mt-2 text-sm border-t-2 border-gray-400 md:pt-0 md:mt-0 md:border-t-0 md:border-l-2 lg:text-base md:pl-11">
        <div v-if="groupsOwned.length || groupsJoined.length" class="flex flex-col gap-5">
            <div>
                <h3 class="font-semibold text-gray-400 lg:text-base dark:text-gray-100">
                    como Propietario
                </h3>
                <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <!-- <GroupItem v-for="group in groupsOwned" :key="group.id" :group="group" /> -->
                    <GroupItem v-for="group in groupsOwned" :key="group.id" :group="group" :is-my-profile="isMyProfile"
                        @callShowConfirmLeaveGroup="showConfirmLeaveGroupFromProfileModal" />
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-400 lg:text-base dark:text-gray-100">
                    como Miembro
                </h3>
                <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <!-- <GroupItem v-for="group in groupsJoined" :key="group.id" :group="group" /> -->
                    <GroupItem v-for="group in groupsJoined" :key="group.id" :group="group" :is-my-profile="isMyProfile"
                        @callShowConfirmLeaveGroup="showConfirmLeaveGroupFromProfileModal" />
                </div>
            </div>
        </div>
        <p v-else class="w-full text-center">
            {{ $t('dearbook.group.list.inside_profile.no_registers') }}
        </p>

        <!-- - - - - -->
        <ConfirmLeaveGroupModal :show="showingConfirmLeaveGroupModal" @close="closeConfirmLeaveGroupModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $t('dearbook.group.confirm.leave_option.modal.question') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('dearbook.group.confirm.leave_option.modal.text') }}
                </p>

                <div class="flex justify-end mt-6">
                    <SecondaryButton @click="closeConfirmLeaveGroupModal" :title="$t('Cancel')"> {{ $t('Cancel') }}
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="leaveGroup"
                        :title="$t('dearbook.group.confirm.leave_option.modal.btn_text')">
                        {{ $t('dearbook.group.confirm.leave_option.modal.btn_text') }}
                    </DangerButton>
                </div>
            </div>
        </ConfirmLeaveGroupModal>
        <!-- - - - - -->
    </div>
</template>
