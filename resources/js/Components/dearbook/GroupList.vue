<script setup>
import GroupItem from '@/Components/dearbook/GroupItem.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')
</script>

<template>
    <div class="px-3">
        <h2 class="text-xl font-bold">{{ $t('dearbook.group.section_label') }}</h2>

        <TextInput class="mt-2" :model-value="searchKeyword" :placeholder="$t('dearbook.group.search.placeholder')" />

        <div class="py-2">
            <div v-if="false" class="flex text-gray-400">
                <p class="w-full text-center">
                    {{ $t('dearbook.group.list.no_registers') }}
                </p>
            </div>

            <div v-else>
                <GroupItem image="https://picsum.photos/100" title="Laravel Fans"
                    description="Lorem ipsum dolor sit amet consectetur adipisicing elit." :class="{'mt-1': false}" />
                <GroupItem image="https://picsum.photos/100" title="Backenderos"
                    description="Reprehenderit, inventore excepturi aut, explicabo unde, id et accusantium dolor."
                    :class="{'mt-1': true}" />
                <GroupItem image="https://picsum.photos/100" title="Donostiarras"
                    description="Tempore deserunt suscipit ratione numquam dolores maiores." :class="{'mt-1': true}" />
            </div>
        </div>
    </div>
</template>
