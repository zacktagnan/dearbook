<script setup>
import TextInput from '@/Components/TextInput.vue'
import UserItem from '@/Components/dearbook/User/Item.vue'
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const props = defineProps({
    followings: {
        type: Array,
    },
})

const followingsCollection = ref(props.followings)
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
        followingsCollection.value = props.followings;
    } else {
        followingsCollection.value = props.followings.filter(following =>
            following.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
            || following.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        );
    }
}

const clearFilter = () => {
    searchKeyword.value = ''
    followingsCollection.value = props.followings
    isFiltering.value = false
}
</script>

<template>
    <div class="relative">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.following.search.inside_profile.placeholder')"
            @keyup="onTypingTerm"
            @keyup.enter="filterList" />

        <button @click="clearFilter" class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150" :class="[
            isFiltering
            ? ''
            : 'hidden'
        ]" :title="$t('dearbook.following.search.inside_profile.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <div class="text-gray-400 text-xs pl-3 h-3">
        <p v-if="isTypingTerm" class="italic">{{ $t('dearbook.following.search.inside_profile.writing') }}</p>
        <p v-if="!isTypingTerm && searchKeyword">{{ $t('dearbook.following.search.inside_profile.push_enter_to_filter') }}</p>
    </div>

    <div v-if="followingsCollection.length" class="grid grid-cols-2 gap-3 mt-3">
        <UserItem v-for="following of followingsCollection" :user="following"
            :classes="' shadow shadow-gray-200 hover:shadow-gray-400 hover:bg-gray-50'"
            :key="following.id" :user-since-date="following.since_date">
        </UserItem>
    </div>
    <div v-else>
        <p v-if="isFiltering" class="w-full text-center mt-3">
            {{ $t('dearbook.following.search.inside_profile.no_registers') }}
        </p>
        <p v-else class="w-full text-center mt-3">
            {{ $t('dearbook.following.list.inside_profile.no_registers') }}
        </p>
    </div>
</template>
