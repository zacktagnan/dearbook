<script setup>
import {
    ArrowDownTrayIcon,
    DocumentIcon,
    HandThumbUpIcon,
    ChatBubbleLeftRightIcon,
} from "@heroicons/vue/24/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

const props = defineProps({
    post: Object,
});

const ellipsis = props.post.body.length > 100 ? "..." : "";

const isImage = (attachment) => {
    const mime = attachment.mime.split("/");
    return mime[0].toLowerCase() === "image";
};
</script>

<template>
    <div class="p-4 mt-4 bg-white rounded shadow hover:shadow-cyan-900">
        <div class="flex items-center gap-2">
            <a href="javascript:void(0)" :title="'Perfil de ' + post.user.name">
                <img
                    :src="post.user.avatar"
                    class="w-[40px] rounded-md border-2 hover:border-cyan-500 transition-all"
                    :alt="post.user.name"
                />
            </a>
            <div class="mt-1 leading-none">
                <h4 class="font-bold">
                    <a
                        href="javascript:void(0)"
                        class="hover:underline"
                        :title="'Perfil de ' + post.user.name"
                        >{{ post.user.name }}</a
                    >
                    <template v-if="post.group">
                        <span class="mx-1 text-cyan-500">></span>
                        <a
                            href="javascript:void(0)"
                            class="hover:underline"
                            :title="'Perfil de ' + post.group.name"
                            >{{ post.group.name }}</a
                        >
                    </template>
                </h4>
                <small class="text-xs text-gray-400">{{
                    post.created_at
                }}</small>
            </div>
        </div>

        <div class="mt-1">
            <Disclosure v-slot="{ open }">
                <div
                    v-if="!open"
                    v-html="post.body.substring(0, 100) + ellipsis"
                />
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
                        <div v-html="post.body" />
                    </DisclosurePanel>
                </transition>
                <hr class="m-1" />
                <div class="flex justify-end">
                    <DisclosureButton
                        class="text-cyan-700 hover:text-cyan-500"
                        :title="open ? 'Mostrar -' : 'Mostrar +'"
                    >
                        {{ open ? "Mostrar -" : "Mostrar +" }}
                    </DisclosureButton>
                </div>
            </Disclosure>
        </div>

        <div
            v-if="post.attachments"
            class="grid grid-cols-2 gap-3 mt-1 lg:grid-cols-3"
        >
            <template v-for="attachment of post.attachments">
                <div
                    class="relative flex flex-col items-center justify-center text-gray-500 aspect-square bg-cyan-100 group"
                >
                    <button
                        class="absolute flex items-center justify-center w-8 h-8 text-gray-100 transition-all bg-gray-600 rounded opacity-0 cursor-pointer group-hover:opacity-100 hover:bg-gray-800 right-2 top-2"
                    >
                        <ArrowDownTrayIcon class="w-5 h-5" />
                    </button>

                    <img
                        v-if="isImage(attachment)"
                        :src="attachment.url"
                        :alt="attachment.name"
                        class="object-cover aspect-square"
                    />

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
                class="flex items-center justify-center flex-1 gap-1 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
                <HandThumbUpIcon class="w-6 h-6" />
                Like
            </button>

            <button
                class="flex items-center justify-center flex-1 gap-1 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
                <ChatBubbleLeftRightIcon class="w-6 h-6" />
                Comentario(s)
            </button>
        </div>
    </div>
</template>
