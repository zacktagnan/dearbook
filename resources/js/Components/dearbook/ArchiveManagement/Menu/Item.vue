<script setup>
defineProps({
    componentName: String,
    title: String,
    isSelected: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['callLoadComponent']);

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}
</script>

<template>
    <div class="transition-colors duration-200 rounded-md cursor-pointer hover:bg-gray-200 hover:text-black" :class="[
        isSelected
            ? 'bg-gray-200 text-black'
            : 'text-gray-500'
    ]">
        <div class="flex items-start gap-2 px-2 py-1">
            <button @click="loadComponent(componentName)" :title="'Ir a ' + title" class="w-full">
                <h3 class="flex items-center gap-2 text-lg font-medium">
                    <slot name="icon"></slot>
                    {{ title }}
                </h3>
            </button>
        </div>
    </div>
</template>
