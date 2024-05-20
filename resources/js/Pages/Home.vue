<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/dearbook/GroupList.vue'
import PostCreate from '@/Components/dearbook/PostCreate.vue'
import PostList from '@/Components/dearbook/PostList.vue'
import FollowingList from '@/Components/dearbook/FollowingList.vue'
import { Head } from '@inertiajs/vue3';

defineProps({
});
</script>

<template>
    <Head :title="$t('Home')" />

    <AuthenticatedLayout>
        <div class="grid gap-3 px-4 pt-20 pb-4 lg:grid-cols-12">
            <div class="lg:col-span-3 lg:order-1">
                <GroupList />
            </div>

            <div class="overflow-hidden lg:col-span-3 lg:order-3">
                <FollowingList />
            </div>

            <div class="flex flex-col overflow-hidden lg:col-span-6 lg:order-2">
                <PostCreate />
                <PostList class="flex-1" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
