<script setup>
import TextInput from '@/Components/TextInput.vue'
import UserItem from '@/Components/dearbook/User/Item.vue'
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const props = defineProps({
    followers: {
        type: Array,
    },
})

const followersCollection = ref(props.followers)
const searchKeyword = ref('')

const isTypingTerm = ref(false)
let typingTermTimeout
const isFiltering = ref(false)

const onTypingTerm = () => {
    isTypingTerm.value = true

    clearTimeout(typingTermTimeout)
    typingTermTimeout = setTimeout(() => {
        isTypingTerm.value = false
    }, 500)
}

const filterList = () => {
    isTypingTerm.value = false
    isFiltering.value = true

    if (searchKeyword.value.trim() === '') {
        followersCollection.value = props.followers;
    } else {
        followersCollection.value = props.followers.filter(follower =>
            follower.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
            || follower.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        );
    }
}

const clearFilter = () => {
    searchKeyword.value = ''
    followersCollection.value = props.followers
    isFiltering.value = false
}
</script>

<template>
    <div class="relative">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.follower.search.inside_profile.placeholder')"
            @keyup="onTypingTerm"
            @keyup.enter="filterList" />

        <button @click="clearFilter" class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150" :class="[
            isFiltering
            ? ''
            : 'hidden'
        ]" :title="$t('dearbook.follower.search.inside_profile.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <div class="text-gray-400 text-xs pl-3 h-3">
        <p v-if="isTypingTerm" class="italic">{{ $t('dearbook.follower.search.inside_profile.writing') }}</p>
        <p v-if="!isTypingTerm && searchKeyword">{{ $t('dearbook.follower.search.inside_profile.push_enter_to_filter') }}</p>
    </div>

    <div v-if="followersCollection.length" class="grid grid-cols-2 gap-3 mt-3">
        <UserItem v-for="follower of followersCollection" :user="follower"
            :classes="' shadow shadow-gray-200 dark:shadow-gray-50 hover:shadow-gray-400 dark:hover:shadow-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900'"
            :key="follower.id" :user-since-date="follower.since_date">
        </UserItem>
    </div>
    <div v-else>
        <p v-if="isFiltering" class="w-full text-center mt-3">
            {{ $t('dearbook.follower.search.inside_profile.no_registers') }}
        </p>
        <p v-else class="w-full text-center mt-3">
            {{ $t('dearbook.follower.list.inside_profile.no_registers') }}
        </p>
    </div>
</template>
