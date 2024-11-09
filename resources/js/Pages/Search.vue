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
    keywords: {
        type: String,
        default: '',
    },
    keywords: String,
    after_comment_deleted: {
        type: Object,
    },
})

const isThereResults = computed(() => props.users.length || props.groups.length || props.posts.data.length)
</script>

<template>

    <Head :title="$t('dearbook.search.index.section_label')" />

    <AuthenticatedLayout>
        <div class="p-4 md:w-1/2 mx-auto">
            <h1 class="text-2xl font-bold">{{ $t('dearbook.search.index.section_label') }}</h1>

            <p class="mt-2">{{ $t('dearbook.search.index.searching_for') }}: "<span class="italic font-medium">{{ keywords }}</span>"</p>

            <template v-if="isThereResults">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-3">
                    <div class="p-3 bg-sky-200 shadow rounded-lg">
                        <h2 class="text-lg font-bold bg-sky-400 text-white px-2 rounded-lg">{{ $t('dearbook.search.index.block_results.title.users') }}</h2>
                        <div class="grid-cols-2 mt-2">
                            <template v-if="users.length">
                                <UserItem v-for="(user, index) of users" :user="user" :classes="' hover:bg-white border-[1px] border-white'" :class="{ 'mt-1': index > 0 }" />
                            </template>
                            <div v-else class="py-2 bg-white rounded shadow text-center">
                                {{ $t('dearbook.search.index.no_results_text.basic') }}
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-sky-200 shadow rounded-lg">
                        <h2 class="text-lg font-bold bg-sky-400 text-white px-2 rounded-lg">{{ $t('dearbook.search.index.block_results.title.groups') }}</h2>
                        <div class="grid-cols-2 mt-2">
                            <template v-if="groups.length">
                                <GroupItem v-for="(group, index) in groups" :group="group" class=" border-[1px] border-white" :class="{ 'mt-1': index > 0 }" />
                            </template>
                            <div v-else class="py-2 bg-white rounded shadow text-center">
                                {{ $t('dearbook.search.index.no_results_text.basic') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-sky-200 shadow rounded-lg mt-4 p-3">
                    <h2 class="text-lg font-bold bg-sky-400 text-white px-2 rounded-lg">{{ $t('dearbook.search.index.block_results.title.posts') }}</h2>
                    <PostList v-if="posts.data.length > 0" class="last:mb-[5px]" :posts="posts.data"
                        :after_comment_deleted="after_comment_deleted" />
                    <div v-else class="py-2 mt-2 bg-white rounded shadow text-center">
                        {{ $t('dearbook.search.index.no_results_text.basic') }}
                    </div>
                </div>
            </template>
            <div v-else class="bg-sky-200 shadow rounded-lg mt-3 p-3">
                <h2 class="text-lg font-bold bg-sky-400 text-white px-2 rounded-lg">{{ $t('dearbook.search.index.block_results.title.no_results') }}</h2>
                <div class="py-2 mt-2 bg-white rounded shadow text-center">
                    {{ $t('dearbook.search.index.no_results_text.for_all') }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
