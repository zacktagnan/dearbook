<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from "@inertiajs/vue3";

defineProps({
    status: {
        type: Array,
    },
});
</script>

<template>
    <GuestLayout>

        <Head :title="status.title" />

        <!-- <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div> -->

        <div class="p-11">
            <h1 class="text-xl font-bold">{{ status.title }}</h1>

            <p class="my-7 text-center">{{ status.message }}</p>

            <div class="flex justify-center">
                <img :src="'/img/' + status.code + '-' + $t('current_language_code') + '.png'" :alt="status.title" />
            </div>
        </div>
    </GuestLayout>
</template>
