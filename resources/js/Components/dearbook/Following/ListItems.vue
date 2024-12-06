<script setup>
import UserItem from '@/Components/dearbook/User/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import { XMarkIcon, } from "@heroicons/vue/24/outline";
import { router } from '@inertiajs/vue3';

const props = defineProps({
    followings: Array,
    searchFollowingTerm: {
        type: String,
        default: '',
    },
})

const searchKeyword = ref(props.searchFollowingTerm)

const followingFiltering = () => {
    let search_term = `search_following_${searchKeyword.value}`
    router.get(route('home', {search_term}))
}

const clearFilter = () => {
    router.get(route('home'))
}

// TEMPORAL
// import { usePage } from "@inertiajs/vue3";
// const authUser = usePage().props.auth.user;
</script>

<template>
    <!-- <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.following.search.main.placeholder')" /> -->
    <div class="relative mt-2">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.following.search.main.placeholder')"
            @keyup.enter="followingFiltering" />

        <button @click="clearFilter" class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150" :class="[
            searchFollowingTerm
            ? ''
            : 'hidden'
        ]" :title="$t('dearbook.following.search.main.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="!followings.length" class="flex p-3 text-gray-400">
            <p v-if="searchFollowingTerm" class="w-full text-center">
                {{ $t('dearbook.following.search.main.no_registers') }}
            </p>
            <p v-else class="w-full text-center">
                {{ $t('dearbook.following.list.main.no_registers') }}
            </p>
        </div>

        <div v-else>
            <!-- <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': false }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" /> -->

            <UserItem v-for="(following, index) in followings" :user="following" :user-since-date="following.since_date" :classes="' hover:bg-white dark:hover:bg-[#102c38]'" :class="{ 'mt-1': index > 0 }" />
        </div>
    </div>
</template>
