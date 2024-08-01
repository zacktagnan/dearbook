<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";

defineProps({
    title: {
        type: String,
    },
    message: {
        type: String,
    },
});

const fadeOutEffect = (className) => {
    var fadeTargetArr = document.getElementsByClassName(className);
    for (let i = 0; i < fadeTargetArr.length; i++) {
        var fadeTarget = fadeTargetArr[i];

        var fadeEffect = setInterval(function () {
            if (!fadeTarget.style.opacity) {
                fadeTarget.style.opacity = 1;
            }
            if (fadeTarget.style.opacity > 0) {
                fadeTarget.style.opacity -= 0.1;
            } else {
                clearInterval(fadeEffect);
            }
        }, 50);
    }

    setTimeout(() => {
        emit('callCloseShowNotification')
    }, 1000)
}

const emit = defineEmits(['callCloseShowNotification', 'callOnMouseOver', 'callOnMouseLeave'])

defineExpose({
    fadeOutEffect,
})
</script>

<template>
    <div class="z-50 w-full whitespace-normal lg:w-1/4 toast notification md::whitespace-nowrap" id="notification-box"
        @mouseover="$emit('callOnMouseOver')" @mouseleave="$emit('callOnMouseLeave')">
        <div class="flex justify-between shadow-lg alert" :class="title === 'Info' ? 'bg-gray-200' : 'bg-red-200'">
            <div class="flex items-center gap-3">
                <svg v-if="title === 'Info'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="w-6 h-6 stroke-info shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-red-600 shrink-0" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex flex-col items-start text-justify">
                    <h3 class="font-bold">{{ title }}</h3>
                    <div class="text-xs">{{ message }}</div>
                </div>
            </div>
            <div>
                <button @click="fadeOutEffect('notification')"
                    class="flex items-center p-1 font-bold text-white bg-red-300 rounded-full hover:bg-red-400"
                    title="Cerrar">
                    <XMarkIcon class="w-3 h-3" />
                </button>
            </div>
        </div>
    </div>
</template>
