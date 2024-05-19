<script setup>
import FollowingItem from '@/Components/dearbook/FollowingItem.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')
</script>

<template>
    <div class="px-3">
        <h2 class="text-xl font-bold">{{ $t('dearbook.following.section_label') }}</h2>

        <TextInput class="mt-2" :model-value="searchKeyword" :placeholder="$t('dearbook.following.search.placeholder')" />

        <div class="py-2">
            <div v-if="false" class="flex text-gray-400">
                <p class="w-full text-center">
                    {{ $t('dearbook.following.list.no_registers') }}
                </p>
            </div>

            <div v-else>
                <FollowingItem image="https://picsum.photos/100" title="Pol Niuman"
                    :class="{'mt-1': false}" />
                <FollowingItem image="https://picsum.photos/100" title="Rover Redfor"
                                        :class="{'mt-1': true}" />
                <FollowingItem image="https://picsum.photos/100" title="Catarine Ross"
                    :class="{'mt-1': true}" />
            </div>
        </div>
    </div>
</template>
