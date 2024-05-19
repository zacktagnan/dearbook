<script setup>
import FollowingItem from '@/Components/dearbook/FollowingItem.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')
</script>

<template>
    <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.following.search.placeholder')" />

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="false" class="flex p-3 text-gray-400">
            <p class="w-full text-center">
                {{ $t('dearbook.following.list.no_registers') }}
            </p>
        </div>

        <div v-else>
            <FollowingItem image="https://picsum.photos/100" title="Pol Niuman" :class="{ 'mt-1': false }" />
            <FollowingItem image="https://picsum.photos/100" title="Rover Redfor" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
            <FollowingItem image="https://picsum.photos/100" title="Catarine Ross" :class="{ 'mt-1': true }" />
        </div>
    </div>
</template>
