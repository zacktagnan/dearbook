<script setup>
import GroupList from '@/Pages/Profile/Partials/GroupInformation/List.vue'

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
    <section>
        <div class="flex flex-col md:flex-row">
            <div class="w-full text-center md:text-left md:w-1/3 lg:max-w-36">
                <header>
                    <h2 class="font-semibold text-gray-400 lg:text-lg dark:text-gray-100">
                        {{ $t('dearbook.group.index.section_label') }}
                    </h2>
                </header>
            </div>

            <GroupList :groups-owned="groupsOwned" :groups-joined="groupsJoined" />
        </div>
    </section>
</template>
