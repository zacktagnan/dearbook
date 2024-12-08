<script setup>
import { XMarkIcon } from '@heroicons/vue/24/solid'

const emit = defineEmits(['callCloseModal'])
</script>

<template>
    <button @click="$emit('callCloseModal')"
        class="flex items-center p-1 font-bold text-gray-500 hover:text-gray-700 dark:text-gray-200 dark:hover:text-white bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-full transition-colors duration-200"
        title="Cerrar">
        <XMarkIcon class="w-5 h-5" />
    </button>
</template>
