<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from "@inertiajs/vue3";

defineProps({
    statusCode: {
        type: String,
    },
    message: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <GuestLayout>

        <Head :title="$t('error.' + statusCode + '.title')" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="p-11">
            <h1 class="text-xl font-bold">{{ $t('error.' + statusCode + '.title') }}</h1>

            <p v-if="message" class="my-7 text-center">{{ message }}</p>
            <p v-else class="my-7 text-center">{{ $t('error.' + statusCode + '.message') }}</p>

            <div class="flex justify-center">
                <img :src="'/img/' + statusCode + '-' + $t('current_language_code') + '.png'"
                    :alt="$t('error.' + statusCode + '.title')" />
            </div>
        </div>
    </GuestLayout>
</template>
