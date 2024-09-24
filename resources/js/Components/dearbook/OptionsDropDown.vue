<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    EllipsisVerticalIcon,
    EllipsisHorizontalIcon,
    PencilIcon,
    TrashIcon,
    ArrowUturnLeftIcon,
    ArchiveBoxIcon,
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
    isTrashed: {
        type: Boolean,
        default: false,
    },
    isArchived: {
        type: Boolean,
        default: false,
    },
    isActivityLog: {
        type: Boolean,
        default: false,
    },
    itemType: String,
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits([
    "callEditItem",
    "callArchiveItem",
    "callDeleteItem",
    "callRestoreItemFromArchive",
    "callRestoreItemFromTrash",
    "callForceDeleteItem",
]);
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
                <template v-if="!isTrashed && !isArchived">
                    <div v-if="!isActivityLog" class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="$emit('callEditItem')" :class="[
                            active
                                ? 'bg-sky-100'
                                : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]" :title="itemType === 'post' ? 'Editar publicación' : 'Editar comentario'">
                            <PencilIcon v-if="showMenuItemIcon" :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                aria-hidden="true" />
                            Editar
                        </button>
                        </MenuItem>
                    </div>

                    <div v-if="itemType === 'post'" class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="$emit('callArchiveItem')" :class="[
                            active
                                ? 'bg-sky-100'
                                : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]" title="Mover al archivo">
                            <ArchiveBoxIcon v-if="showMenuItemIcon" :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                aria-hidden="true" />
                            Al archivo
                        </button>
                        </MenuItem>
                    </div>

                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                        <button @click="$emit('callDeleteItem')" :class="[
                            active
                                ? 'bg-sky-100'
                                : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]" :title="itemType === 'post' ? 'Mover a la papelera' : 'Eliminar'">
                            <TrashIcon v-if="showMenuItemIcon" :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                aria-hidden="true" />
                            {{ itemType === 'post' ? 'A la papelera' : 'Eliminar' }}
                        </button>
                        </MenuItem>
                    </div>
                </template>
                <template v-else>
                    <template v-if="isTrashed">
                        <div v-if="itemType === 'post'" class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callArchiveItem')" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Mover al archivo">
                                <ArchiveBoxIcon v-if="showMenuItemIcon" :active="active"
                                    class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Al archivo
                            </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callRestoreItemFromTrash')" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Restaurar publicación">
                                <ArrowUturnLeftIcon v-if="showMenuItemIcon" :active="active"
                                    class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Restaurar
                            </button>
                            </MenuItem>
                        </div>

                        <!-- Momentáneamente, desactivado para cuando itemType === 'group' -->
                        <div v-if="itemType !== 'group'" class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callForceDeleteItem')" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Eliminar del todo">
                                <TrashIcon v-if="showMenuItemIcon" :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                    aria-hidden="true" />
                                Eliminar
                            </button>
                            </MenuItem>
                        </div>
                    </template>
                    <template v-if="isArchived">
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callRestoreItemFromArchive')" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Restaurar publicación">
                                <ArrowUturnLeftIcon v-if="showMenuItemIcon" :active="active"
                                    class="w-5 h-5 mr-2 text-sky-400" aria-hidden="true" />
                                Restaurar
                            </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="$emit('callDeleteItem')" :class="[
                                active
                                    ? 'bg-sky-100'
                                    : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Mover a la papelera">
                                <TrashIcon v-if="showMenuItemIcon" :active="active" class="w-5 h-5 mr-2 text-sky-400"
                                    aria-hidden="true" />
                                A la papelera
                            </button>
                            </MenuItem>
                        </div>
                    </template>
                </template>
            </MenuItems>
        </transition>
    </Menu>
</template>
