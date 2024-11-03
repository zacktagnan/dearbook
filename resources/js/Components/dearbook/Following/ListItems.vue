<script setup>
import UserItem from '@/Components/dearbook/User/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')

// TEMPORAL
import { usePage } from "@inertiajs/vue3";
const authUser = usePage().props.auth.user;
</script>

<template>
    <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.following.search.main.placeholder')" />

    <div class="h-[200px] lg:h-[250px] lg:flex-1 py-2 mt-1 overflow-auto">
        <div v-if="false" class="flex p-3 text-gray-400">
            <p class="w-full text-center">
                {{ $t('dearbook.following.list.main.no_registers') }}
            </p>
        </div>

        <div v-else>
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': false }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
            <UserItem :user="authUser" :classes="' hover:bg-white'" :class="{ 'mt-1': true }" />
        </div>
    </div>
</template>
