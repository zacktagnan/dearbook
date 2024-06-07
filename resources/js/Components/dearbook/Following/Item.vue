<script setup>

defineProps({
    image: {
        type: String,
    },
    title: String,
});

</script>

<template>
    <div class="rounded-md cursor-pointer hover:bg-gray-100">
        <div class="flex items-center gap-2 px-2 py-1">
            <img :src="image" class="mt-1 w-[36px] rounded-full" :alt="title">
            <div>
                <h3 class="text-lg font-black">{{ title }}</h3>
            </div>
        </div>
    </div>
</template>
