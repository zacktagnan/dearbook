<script setup>
import GroupItem from '@/Components/dearbook/Group/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import { PlusIcon } from "@heroicons/vue/24/outline";

defineProps({
    'groups': Array,
})

defineEmits([
    "callOpenCreateGroupModal",
])

const searchKeyword = ref('')
</script>

<template>
    <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.group.search.placeholder')" />

    <button @click="$emit('callOpenCreateGroupModal')" :title="$t('dearbook.group.form.btn_init_creation')"
        class="w-full bg-sky-400 text-gray-200 transition-colors delay-150 flex justify-center items-center rounded-md mt-3 hover:bg-[#0099ce] hover:text-white">
        <div class="flex py-1 gap-2">
            <PlusIcon class="w-4 h-4 mt-1" />
            {{ $t('dearbook.group.form.btn_init_creation') }}
        </div>
    </button>

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="false" class="flex p-3 text-gray-400">
            <p class="w-full text-center">
                {{ $t('dearbook.group.list.no_registers') }}
            </p>
        </div>

        <div v-else>
            <GroupItem v-for="(group, index) in groups" :group="group" :class="{ 'mt-1': index > 0 }" />
        </div>
    </div>
</template>
