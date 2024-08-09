<script setup>

defineProps({
    posts: [String, Object],
});
</script>

<template>
    <div>
        Activity Log - Pages
        <br>
        <br>
        <pre>
            {{ posts }}
        </pre>
    </div>
</template>
