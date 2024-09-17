<script setup>
defineProps({
    selected: Boolean,
    text: String,
});
</script>

<template>
    <button :class="[
        'px-3 py-2.5 outline-none text-xs md:text-sm ml-1 text-nowrap',
        selected
            ? 'bg-white text-cyan-500 border-b-2 border-cyan-500'
            : 'text-gray-400 hover:text-gray-600 border-b-2 border-white hover:border-gray-600',
    ]">
        {{ text }}
    </button>
</template>
