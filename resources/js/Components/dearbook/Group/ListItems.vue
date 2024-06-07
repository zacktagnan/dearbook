<script setup>
import GroupItem from '@/Components/dearbook/Group/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')
</script>

<template>
    <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.group.search.placeholder')" />

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="false" class="flex p-3 text-gray-400">
            <p class="w-full text-center">
                {{ $t('dearbook.group.list.no_registers') }}
            </p>
        </div>

        <div v-else>
            <GroupItem image="https://picsum.photos/100" title="Laravel Fans"
                description="Lorem ipsum dolor sit amet consectetur adipisicing elit." :class="{ 'mt-1': false }" />
            <GroupItem image="https://picsum.photos/100" title="Backenderos"
                description="Reprehenderit, inventore excepturi aut, explicabo unde, id et accusantium dolor."
                :class="{ 'mt-1': true }" />
            <GroupItem image="https://picsum.photos/100" title="Donostiarras"
                description="Tempore deserunt suscipit ratione numquam dolores maiores." :class="{ 'mt-1': true }" />
            <GroupItem image="https://picsum.photos/100" title="Donostiarras"
                description="Tempore deserunt suscipit ratione numquam dolores maiores." :class="{ 'mt-1': true }" />
            <GroupItem image="https://picsum.photos/100" title="Donostiarras"
                description="Tempore deserunt suscipit ratione numquam dolores maiores." :class="{ 'mt-1': true }" />
            <GroupItem image="https://picsum.photos/100" title="Donostiarras"
                description="Tempore deserunt suscipit ratione numquam dolores maiores." :class="{ 'mt-1': true }" />
        </div>
    </div>
</template>
