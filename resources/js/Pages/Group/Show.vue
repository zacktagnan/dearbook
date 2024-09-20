<script setup>
import ProfileInformation from "./Partials/ProfileInformation.vue";

defineProps({
    group: {
        type: Object,
    },
});
</script>

<template>
    <div class="md:w-[55%] mx-auto">
        <ProfileInformation :group="group" />
    </div>
</template>
