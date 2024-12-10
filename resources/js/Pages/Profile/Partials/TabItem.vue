<script setup>
defineProps({
    selected: Boolean,
    text: String,
});
</script>

<template>
    <button
        :class="[
            'px-3 py-2.5 outline-none text-xs md:text-sm ml-1 text-nowrap',
            selected
                ? 'bg-white dark:bg-gray-800 text-cyan-500 border-b-2 border-cyan-500'
                : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 border-b-2 border-white hover:border-gray-600 dark:border-gray-800 dark:hover:border-gray-200',
        ]"
    >
        {{ text }}
    </button>
</template>
