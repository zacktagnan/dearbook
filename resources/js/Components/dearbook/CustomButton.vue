<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    generalClasses: {
        type: String,
        default: '',
    },
    colorClasses: {
        type: String,
        default: 'text-indigo-900 bg-indigo-100 hover:bg-indigo-200 focus-visible:ring-indigo-500',
    },
    otherClasses: {
        type: String,
        default: '',
    },
})

const classes = ' ' + props.generalClasses + ' ' + props.colorClasses + ' ' + props.otherClasses
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border border-transparent rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
        :class="classes"
    >
        <slot />
    </button>
</template>
