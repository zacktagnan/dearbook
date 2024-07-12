<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    EllipsisVerticalIcon,
    EllipsisHorizontalIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import { computed } from "vue";

const props = defineProps({
    ellipsisTypeIcon: String,
    menuButtonClasses: {
        type: String,
        default: '',
    },
    menuItemsClasses: {
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

const emit = defineEmits([
    "callEditItem",
    "callDeleteItem",
]);
</script>

<template>
    <Menu
        v-if="show"
        as="div"
        class="relative inline-block text-left"
    >
        <div>
            <MenuButton
                class="p-1 transition-colors duration-150 rounded-full hover:bg-black/5"
                :class="menuButtonClasses"
                title="Ver opciones"
            >
                <EllipsisVerticalIcon
                    v-if="ellipsisTypeIcon === 'vertical'"
                    class="w-5 h-5"
                    aria-hidden="true"
                />
                <EllipsisHorizontalIcon
                    v-else-if="ellipsisTypeIcon === 'horizontal'"
                    class="w-5 h-5"
                    aria-hidden="true"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg z-[11] w-28 ring-1 ring-black/5 focus:outline-none"
                :class="menuItemsClasses"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <button
                            @click="$emit('callEditItem')"
                            :class="[
                                active
                                    ? 'bg-indigo-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                            title="Edit"
                        >
                            <PencilIcon
                                v-if="showMenuItemIcon"
                                :active="active"
                                class="w-5 h-5 mr-2 text-indigo-400"
                                aria-hidden="true"
                            />
                            Edit
                        </button>
                    </MenuItem>
                </div>

                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <button
                            @click="$emit('callDeleteItem')"
                            :class="[
                                active
                                    ? 'bg-indigo-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                            title="Eliminar"
                        >
                            <TrashIcon
                                v-if="showMenuItemIcon"
                                :active="active"
                                class="w-5 h-5 mr-2 text-indigo-400"
                                aria-hidden="true"
                            />
                            Eliminar
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
