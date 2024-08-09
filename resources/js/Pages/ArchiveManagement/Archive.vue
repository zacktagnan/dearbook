<script setup>

defineProps({
    posts: [String, Object],
});
</script>

<template>
    <div>
        Archive - Pages
        <br>
        <br>
        <pre>
            {{ posts }}
        </pre>
    </div>
</template>
