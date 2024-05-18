<script setup>
import GroupList from '@/Components/dearbook/GroupList.vue'
import FollowingList from '@/Components/dearbook/FollowingList.vue'
import { Head } from '@inertiajs/vue3';

defineProps({
});
</script>

<template>

    <Head :title="$t('Home')" />
    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-3 py-6">
            <GroupList />
        </div>

        <div class="col-span-6 bg-yellow-500">
            Publicaciones
        </div>

        <div class="col-span-3 py-6">
            <FollowingList />
        </div>
    </div>
</template>
