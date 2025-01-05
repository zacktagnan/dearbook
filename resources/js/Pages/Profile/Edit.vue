<script setup>
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import GroupInformation from '@/Pages/Profile/Partials/GroupInformation/Index.vue'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    groupsOwned: {
        type: Array,
    },
    groupsJoined: {
        type: Array,
    },
    isMyProfile: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="my-6 space-y-6">
        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
        </div>

        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <UpdatePasswordForm class="max-w-xl" />
        </div>

        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <DeleteUserForm class="max-w-xl" />
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow sm:p-8 sm:rounded-lg">
            <GroupInformation :groups-owned="groupsOwned" :groups-joined="groupsJoined" :is-my-profile="isMyProfile" />
        </div>
    </div>
</template>
