<script setup>
import { ref, computed } from "vue";
import {
    ArrowDownTrayIcon,
    DocumentIcon,
    HandThumbUpIcon,
    ChatBubbleLeftRightIcon,
    EllipsisVerticalIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

const props = defineProps({
    post: Object,
});

const emit = defineEmits('callOpenEditModal')

const largeBodyLength = 100

const bodyExcerpt = ref('')
const getBodyExcerpt = computed(() => {
    if (props.post.body) {
        const ellipsis = props.post.body.length > largeBodyLength ? "..." : "";
        bodyExcerpt.value = props.post.body.substring(0, largeBodyLength) + ellipsis
    }
    return bodyExcerpt.value
})

const isImage = (attachment) => {
    const mime = attachment.mime.split("/");
    return mime[0].toLowerCase() === "image";
};

// =======================================================================================

import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import PostHeader from '@/Components/dearbook/PostHeader.vue'
import { router, usePage } from "@inertiajs/vue3";

const openEditModal = () => {
    emit('callOpenEditModal', props.post)
}

const deletePost = () => {
    if (window.confirm('Are you sure you want to DELETE this post?')) {
        router.delete(route('post.destroy', props.post), {
            preserveScroll: true,
        })
    }
}

// =======================================================================================

const authUser = usePage().props.auth.user

const isPostAuthor = computed(() => authUser && authUser.id === props.post.user.id)
</script>

<template>
    <div class="p-4 mt-4 mx-0.5 bg-white rounded shadow hover:shadow-cyan-900">
        <div class="flex items-center justify-between">
            <PostHeader :post="post" />

            <!-- {{ authUser }}<br>
            {{ authUser.id }}<br>
            {{ post.user_id }}<br>
            {{ isPostAuthor }} -->
            <!-- =========================================================== -->
            <Menu v-if="isPostAuthor" as="div" class="relative inline-block text-left">
                <div>
                    <MenuButton class="p-1 transition-colors duration-150 rounded-full hover:bg-black/5"
                        title="Ver opciones">
                        <EllipsisVerticalIcon class="w-5 h-5" aria-hidden="true" />
                    </MenuButton>
                </div>

                <transition enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <MenuItems
                        class="absolute right-0 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg w-28 ring-1 ring-black/5 focus:outline-none">
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="openEditModal" :class="[
                                    active ? 'bg-indigo-100' : 'text-gray-900',
                                    'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]" title="Edit">
                                <PencilIcon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                Edit
                            </button>
                            </MenuItem>
                        </div>

                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <button @click="deletePost" :class="[
                                active ? 'bg-indigo-100' : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]" title="Eliminar">
                                <TrashIcon :active="active" class="w-5 h-5 mr-2 text-indigo-400" aria-hidden="true" />
                                Eliminar
                            </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
            <!-- =========================================================== -->
        </div>

        <div class="mt-1">
            <Disclosure v-slot="{ open }">
                <template v-if="!post.body">
                    <div class="tooltip tooltip-right" data-tip="Nada reseñado aún...">
                        <p class="inline-block px-1.5 py-0.5 text-white rounded-md bg-slate-500">
                            zzZz...
                        </p>
                    </div>
                </template>
                <template v-else>
                    <!-- Sin estilar para el CKEditor -->
                    <!-- <div v-if="!open" class="whitespace-pre-line">
                        {{ getBodyExcerpt }}
                    </div> -->
                    <div v-if="!open" class="whitespace-pre-line ck-content-output" v-html="getBodyExcerpt" />
                </template>
                <template v-if="post.body && post.body.length > largeBodyLength">
                    <!-- <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-out"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"> -->
                    <transition enter-active-class="transition-opacity duration-75" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150"
                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                        <DisclosurePanel>
                            <!-- Sin estilar para el CKEditor -->
                            <!-- <div class="whitespace-pre-line" v-html="post.body" /> -->
                            <div class="whitespace-pre-line ck-content-output" v-html="post.body" />
                        </DisclosurePanel>
                    </transition>
                    <hr class="m-1" />
                    <div class="flex justify-end">
                        <DisclosureButton class="text-cyan-700 hover:text-cyan-500"
                            :title="open ? 'Mostrar -' : 'Mostrar +'">
                            {{ open ? "Mostrar -" : "Mostrar +" }}
                        </DisclosureButton>
                    </div>
                </template>
            </Disclosure>
        </div>

        <div v-if="post.attachments" class="grid grid-cols-2 gap-3 mt-1 lg:grid-cols-3">
            <template v-for="attachment of post.attachments">
                <div
                    class="relative flex flex-col items-center justify-center text-gray-500 aspect-square bg-cyan-100 group">
                    <button
                        class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-2 top-2">
                        <ArrowDownTrayIcon class="w-5 h-5" />
                    </button>

                    <img v-if="isImage(attachment)" :src="attachment.url" :alt="attachment.name"
                        class="object-cover aspect-square" />

                    <template v-else>
                        <DocumentIcon class="w-12 h-12 lg:w-16 lg:h-16" />

                        <span class="text-sm lg:text-base">{{
                            attachment.name
                            }}</span>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex gap-2 mt-3">
            <button
                class="flex items-center justify-center flex-1 gap-1 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                <HandThumbUpIcon class="w-6 h-6" />
                Like
            </button>

            <button
                class="flex items-center justify-center flex-1 gap-1 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                <ChatBubbleLeftRightIcon class="w-6 h-6" />
                Comentario(s)
            </button>
        </div>
    </div>
</template>
