<script setup>
const props = defineProps({
    componentName: String,
    title: String,
    showingNavigationDropdown: Boolean
});

const emit = defineEmits(['callLoadComponent', 'callShowingNavigationDropdown']);

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName, props.showingNavigationDropdown)
}
</script>

<template>
    <div
        class="transition-colors duration-200 cursor-pointer hover:bg-gray-200 hover:text-black block w-full ps-3 pe-4 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
        <div class="flex items-start gap-2 px-2 py-1">
            <button @click="loadComponent(componentName)" :title="'Ir a ' + title" class="w-full">
                <h3 class="flex items-center gap-2">
                    <slot name="icon"></slot>
                    {{ title }}
                </h3>
            </button>
        </div>
    </div>
</template>
