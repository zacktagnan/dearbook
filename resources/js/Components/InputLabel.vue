<script setup>
defineProps({
    value: {
        type: String,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label :disabled="disabled" class="block font-medium text-sm"
        :class="[
            disabled
                ? 'text-gray-400 dark:text-gray-500'
                : 'text-gray-700 dark:text-gray-300'
        ]">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
