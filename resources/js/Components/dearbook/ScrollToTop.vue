<script setup>
import { ChevronUpIcon } from '@heroicons/vue/24/solid';
import { onMounted, onUnmounted, ref } from 'vue';

const isVisible = ref(false)

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})

const handleScroll = () => {
    // if (window.pageYOffset > 500) // pageYOffset se indica como deprecado
    if (window.scrollY > 500) {
        isVisible.value = true
    } else {
        isVisible.value = false
    }
}
const scrollToTop = () => {
    // scroll.animateScroll(0)
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}
</script>

<template>
    <Transition>
        <button v-if="isVisible" @click="scrollToTop" title="Subir..."
            class="fixed p-1 transition-opacity duration-300 bg-white rounded-full shadow-md opacity-50 right-5 bottom-4 text-cyan-500 hover:opacity-100">
            <ChevronUpIcon class="w-7 h-7" />
        </button>
    </Transition>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
