<script setup>
import GroupItem from '@/Pages/Profile/Partials/GroupInformation/Item.vue'

defineProps({
    groupsOwned: {
        type: Array,
    },
    groupsJoined: {
        type: Array,
    },
});
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
                    <GroupItem v-for="group in groupsOwned" :key="group.id" :group="group" />
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-400 lg:text-base dark:text-gray-100">
                    como Miembro
                </h3>
                <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <GroupItem v-for="group in groupsJoined" :key="group.id" :group="group" />
                </div>
            </div>
        </div>
        <p v-else class="w-full text-center">
            {{ $t('dearbook.group.list.inside_profile.no_registers') }}
        </p>
    </div>
</template>
