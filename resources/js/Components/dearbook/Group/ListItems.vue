<script setup>
import GroupItem from '@/Components/dearbook/Group/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import { PlusIcon, XMarkIcon, } from "@heroicons/vue/24/outline";
import { router } from '@inertiajs/vue3';

const props = defineProps({
    groups: Array,
    searchGroupTerm: {
        type: String,
        default: '',
    },
})

defineEmits([
    "callOpenCreateGroupModal",
])

const searchKeyword = ref(props.searchGroupTerm)

const groupFiltering = () => {
    let search_term = `search_group_${searchKeyword.value}`
    router.get(route('home', {search_term}))
}

const clearFilter = () => {
    router.get(route('home'))
}
</script>

<template>
    <div class="relative mt-2">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.group.search.main.placeholder')"
            @keyup.enter="groupFiltering" />

        <button @click="clearFilter" class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150" :class="[
            searchGroupTerm
            ? ''
            : 'hidden'
        ]" :title="$t('dearbook.group.search.main.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <button @click="$emit('callOpenCreateGroupModal')" :title="$t('dearbook.group.form.create.btn_init_creation')"
        class="w-full bg-sky-400 text-gray-200 transition-colors delay-150 flex justify-center items-center rounded-md mt-3 hover:bg-[#0099ce] hover:text-white">
        <div class="flex py-1 gap-2">
            <PlusIcon class="w-4 h-4 mt-1" />
            {{ $t('dearbook.group.form.create.btn_init_creation') }}
        </div>
    </button>

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="!groups.length" class="flex p-3 text-gray-400">
            <p v-if="searchGroupTerm" class="w-full text-center">
                {{ $t('dearbook.group.search.main.no_registers') }}
            </p>
            <p v-else class="w-full text-center">
                {{ $t('dearbook.group.list.main.no_registers') }}
            </p>
        </div>

        <div v-else>
            <GroupItem v-for="(group, index) in groups" :group="group" :class="{ 'mt-1': index > 0 }" />
        </div>
    </div>
</template>
