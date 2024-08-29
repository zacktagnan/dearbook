<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from "@inertiajs/vue3";

defineProps({
    status: {
        type: String,
    },
});
</script>

<template>
    <GuestLayout>

        <Head :title="$t('error.403.title')" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="p-11">
            <h1 class="text-xl font-bold">{{ $t('error.403.title') }}</h1>

            <p class="my-7 text-center">{{ $t('error.403.message') }}</p>

            <div class="flex justify-center">
                <img :src="'/img/403-' + $t('current_language_code') + '.png'" :alt="$t('error.403.title')" />
            </div>
        </div>
    </GuestLayout>
</template>
