<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3"
import UserItem from '@/Components/dearbook/User/Item.vue'
import GroupItem from '@/Components/dearbook/Group/Item.vue'
import PostList from "@/Components/dearbook/Post/List.vue"
import { computed } from "vue"

const props = defineProps({
    users: Array,
    groups: Array,
    posts: Object,
    postsTotalCount: Number,
    keywords: String,
    after_comment_deleted: {
        type: Object,
    },
})

const isThereResults = computed(() => props.users.length || props.groups.length || props.posts.data.length)

const totalResults = props.users.length + props.groups.length + props.postsTotalCount
</script>

<template>

    <Head :title="$t('dearbook.search.index.section_label')" />

    <AuthenticatedLayout>
        <div class="p-4 md:w-1/2 mx-auto">
            <h1 class="text-2xl font-bold dark:text-gray-100">{{ $t('dearbook.search.index.section_label') }}</h1>

            <div class="flex justify-between items-end px-4 dark:text-gray-100">
                <p class="mt-2">{{ $t('dearbook.search.index.searching_for') }}: "<span class="italic font-medium">{{ keywords }}</span>"</p>
                <span v-if="totalResults > 0" class="font-bold cursor-pointer" title="Cantidad total de registros">#{{ totalResults }}</span>
            </div>

            <template v-if="isThereResults">
                <div v-if="!keywords.startsWith('#')" class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-3">
                    <div class="p-3 bg-sky-200 dark:bg-sky-900 shadow rounded-lg">
                        <div class="flex justify-between items-center bg-sky-400 dark:bg-sky-600 text-white px-2 rounded-lg">
                            <h2 class="text-lg font-bold">{{ $t('dearbook.search.index.block_results.title.users') }}</h2>
                            <span v-if="users.length > 0" class="font-bold mt-0.5 cursor-pointer" title="Total de registros">#{{ users.length }}</span>
                        </div>
                        <div class="grid-cols-2 mt-2">
                            <template v-if="users.length">
                                <UserItem v-for="(user, index) of users" :user="user" :classes="' hover:bg-white dark:hover:bg-[#102c38] border-[1px] border-white dark:text-gray-100'" :class="{ 'mt-1': index > 0 }" />
                            </template>
                            <div v-else class="py-2 bg-white dark:bg-slate-800 dark:text-gray-100 rounded shadow text-center">
                                {{ $t('dearbook.search.index.no_results_text.basic') }}
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-sky-200 dark:bg-sky-900 shadow rounded-lg">
                        <div class="flex justify-between items-center bg-sky-400 dark:bg-sky-600 text-white px-2 rounded-lg">
                            <h2 class="text-lg font-bold">{{ $t('dearbook.search.index.block_results.title.groups') }}</h2>
                            <span v-if="groups.length > 0" class="font-bold mt-0.5 cursor-pointer" title="Total de registros">#{{ groups.length }}</span>
                        </div>
                        <div class="grid-cols-2 mt-2">
                            <template v-if="groups.length">
                                <GroupItem v-for="(group, index) in groups" :group="group" class=" border-[1px] border-white dark:text-gray-100" :class="{ 'mt-1': index > 0 }" />
                            </template>
                            <div v-else class="py-2 bg-white dark:bg-slate-800 dark:text-gray-100 rounded shadow text-center">
                                {{ $t('dearbook.search.index.no_results_text.basic') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-sky-200 dark:bg-sky-900 shadow rounded-lg mt-4 p-3">
                    <div class="flex justify-between items-center bg-sky-400 dark:bg-sky-600 text-white px-2 rounded-lg">
                        <h2 class="text-lg font-bold">{{ $t('dearbook.search.index.block_results.title.posts') }}</h2>
                        <span v-if="postsTotalCount > 0" class="font-bold mt-0.5 cursor-pointer" title="Total de registros">#{{ postsTotalCount }}</span>
                    </div>
                    <PostList v-if="posts.data.length > 0" class="last:mb-[5px]" :posts="posts.data"
                        :after_comment_deleted="after_comment_deleted" />
                    <div v-else class="py-2 mt-2 bg-white dark:bg-slate-800 dark:text-gray-100 rounded shadow text-center">
                        {{ $t('dearbook.search.index.no_results_text.basic') }}
                    </div>
                </div>
            </template>
            <div v-else class="bg-sky-200 dark:bg-sky-900 shadow rounded-lg mt-3 p-3">
                <h2 class="text-lg font-bold bg-sky-400 dark:bg-sky-600 text-white px-2 rounded-lg">{{ $t('dearbook.search.index.block_results.title.no_results') }}</h2>
                <div class="py-2 mt-2 bg-white dark:bg-slate-800 dark:text-gray-100 rounded shadow text-center">
                    {{ $t('dearbook.search.index.no_results_text.for_all') }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
