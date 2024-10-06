<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    EllipsisVerticalIcon,
    EllipsisHorizontalIcon,
} from "@heroicons/vue/24/solid";
import { computed } from "vue";

const props = defineProps({
    ellipsisTypeIcon: String,
    menuButtonClasses: {
        type: String,
        default: '',
    },
    showMenuItemIcon: {
        type: Boolean,
        default: true,
    },
    modelValue: Boolean,
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
</script>

<template>
    <Menu v-if="show" as="div" class="relative inline-block text-left">
        <div>
            <MenuButton class="p-1 transition-colors duration-150 rounded-full hover:bg-black/5"
                :class="menuButtonClasses" title="Ver opciones">
                <EllipsisVerticalIcon v-if="ellipsisTypeIcon === 'vertical'" class="w-5 h-5" aria-hidden="true" />
                <EllipsisHorizontalIcon v-else-if="ellipsisTypeIcon === 'horizontal'" class="w-5 h-5"
                    aria-hidden="true" />
            </MenuButton>
        </div>

        <transition enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0">
            <MenuItems
                class="absolute right-0 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg z-[11] ring-1 ring-black/5 focus:outline-none"
                :class="[
                    showMenuItemIcon
                        ? 'w-36'
                        : 'w-[74px]'
                ]">
                <slot />
            </MenuItems>
        </transition>
    </Menu>
</template>
