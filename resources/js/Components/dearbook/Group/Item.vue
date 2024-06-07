<script setup>

defineProps({
    // canLogin: {
    //     type: Boolean,
    // },
    image: {
        type: String,
    },
    title: String,
    description: String,
});

</script>

<template>
    <div class="rounded-md cursor-pointer hover:bg-gray-100">
        <div class="flex items-start gap-2 px-2 py-1">
            <img :src="image" class="mt-1 w-[36px] rounded-full" :alt="title">
            <div>
                <h3 class="text-lg font-black">{{ title }}</h3>
                <div class="text-xs text-gray-500">{{ description }}</div>
            </div>
        </div>
    </div>
</template>
