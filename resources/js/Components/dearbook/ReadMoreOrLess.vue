<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { PlusCircleIcon, MinusCircleIcon } from "@heroicons/vue/24/outline";
import { ref, computed } from "vue";

const props = defineProps({
    content: String,
    maxContentLength: Number,
    contentClasses: String,
})

const contentExcerpt = ref("");
const getContentExcerpt = computed(() => {
    if (props.content) {
        const ellipsis = props.content.length > props.maxContentLength ? "..." : "";
        contentExcerpt.value =
            props.content.substring(0, props.maxContentLength) + ellipsis;
    }
    return contentExcerpt.value;
});
</script>

<template>
    <Disclosure v-slot="{ open }">
        <template v-if="!content">
            <div class="tooltip tooltip-right" data-tip="Nada reseñado aún...">
                <p
                    class="inline-block px-1.5 py-0.5 text-white rounded-md bg-slate-500"
                >
                    zzZz...
                </p>
            </div>
        </template>
        <template v-else>
            <!-- Sin estilar para el CKEditor -->
            <!-- <div v-if="!open" class="whitespace-pre-line">
                {{ getContentExcerpt }}
            </div> -->
            <div
                v-if="!open"
                class="whitespace-pre-line ck-content-output"
                :class="contentClasses"
                v-html="getContentExcerpt"
            />
        </template>

        <template v-if="content && content.length > maxContentLength">
            <!-- <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-out"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"> -->
            <transition
                enter-active-class="transition-opacity duration-75"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <DisclosurePanel>
                    <!-- Sin estilar para el CKEditor -->
                    <!-- <div class="whitespace-pre-line" v-html="content" /> -->
                    <div
                        class="whitespace-pre-line ck-content-output"
                        :class="contentClasses"
                        v-html="content"
                    />
                </DisclosurePanel>
            </transition>
            <div class="flex justify-end">
                <DisclosureButton
                    class="flex items-center w-5 h-5 text-cyan-700 hover:text-cyan-500"
                    :title="open ? 'Mostrar -' : 'Mostrar +'"
                >
                    <!-- {{ open ? "-" : "+" }} -->
                    <MinusCircleIcon v-if="open" />
                    <PlusCircleIcon v-else />
                </DisclosureButton>
            </div>
        </template>
    </Disclosure>
</template>
