<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
} from "@headlessui/vue"
import { computed } from "vue"

import { twMerge } from 'tailwind-merge'

const props = defineProps({
    modelValue: Boolean,
    zIndex: {
        type: String,
        required: true,
    },
    subContainerDefaultClasses: {
        type: String,
        default: 'flex items-center justify-center min-h-full p-4 text-center',
    },
    dialogPanelDefaultClasses: {
        type: String,
        default: 'w-full overflow-hidden text-left align-middle transition-all transform dark:text-gray-100 bg-white dark:bg-slate-900 rounded-md shadow-xl dark:shadow-lg dark:shadow-gray-500',
    },
    dialogPanelExtraClasses: {
        type: String,
        default: '',
    },
})

const emit = defineEmits('callCloseModal')

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
})

const dialogDefaultClasses = 'relative'
const dialogMergeClasses = computed(() => {
    return twMerge(
        dialogDefaultClasses,
        props.zIndex
    )
})

const dialogPanelMergeClasses = computed(() => {
    return twMerge(
        props.dialogPanelDefaultClasses,
        props.dialogPanelExtraClasses
    )
})
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="$emit('callCloseModal')" :class="dialogMergeClasses">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0"
                    enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25 dark:bg-black/40" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div :class="subContainerDefaultClasses">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100" leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel
                                :class="dialogPanelMergeClasses"
                            >
                                <slot />
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
