<script setup>
import ProfileInformation from '@/Pages/Profile/Partials/ProfileInformation.vue';
import GroupInformation from '@/Pages/Profile/Partials/GroupInformation/Index.vue'

defineProps({
    user: {
        type: Object,
    },
    groupsOwned: {
        type: Array,
    },
    groupsJoined: {
        type: Array,
    },
});
</script>

<template>
    <div class="mt-2 space-y-6">
        <div
            class="px-4 bg-white shadow md:px-8 sm:py-4 dark:bg-gray-800 sm:rounded-lg"
        >
            <h2
                class="text-lg font-semibold text-gray-800 md:leading-tight lg:text-xl dark:text-gray-200"
            >
                {{ $t("About") }}
            </h2>
        </div>
    </div>

    <div class="mt-4 space-y-6">
        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <ProfileInformation :user="user" />
        </div>
        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <GroupInformation :groups-owned="groupsOwned" :groups-joined="groupsJoined" />
        </div>
    </div>
</template>
