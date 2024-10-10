<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GroupList from "@/Components/dearbook/Group/List.vue";
import PostCreate from "@/Components/dearbook/Post/Create.vue";
import PostList from "@/Components/dearbook/Post/List.vue";
import FollowingList from "@/Components/dearbook/Following/List.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    // Aquí, por ser una consulta paginada "posts" llega como Object, luego, al List se manda "posts.data" que ya se trata de un Array
    posts: Object,
    // En cambio, no es una consulta paginada y ya llega como un Array de Objects
    groups: Array,
    after_comment_deleted: {
        type: Object,
    },
});
</script>

<template>

    <Head :title="$t('Home')" />

    <AuthenticatedLayout>
        <div class="grid gap-3 px-4 top-5 relative pb-4 lg:grid-cols-12">
            <div class="lg:col-span-3 lg:order-1">
                <GroupList :groups="groups" />
            </div>

            <div class="overflow-hidden lg:col-span-3 lg:order-3 lg:w-full lg:max-w-[455px]">
                <FollowingList />
            </div>

            <div class="flex flex-col overflow-hidden lg:col-span-6 lg:order-2">
                <PostCreate />
                <PostList v-if="posts.data.length > 0" class="flex-1 last:mb-[5px]" :posts="posts.data"
                    :after_comment_deleted="after_comment_deleted" />
                <div v-else class="p-4 mx-0.5 bg-white mt-4 rounded shadow text-center">No hay publicaciones actualmente
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
