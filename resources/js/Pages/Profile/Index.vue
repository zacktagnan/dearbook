<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import Show from "@/Pages/Profile/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { CameraIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { Head } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    },
});

const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);
</script>

<template>
    <Head :title="$t('Profile')" />

    <AuthenticatedLayout>
        <!-- <div class="container pt-20 mx-auto "> -->
        <div class="bg-white">
            <div class="lg:w-2/3 mx-auto pt-[58px]">
                <div class="relative bg-white">
                    <img
                        :src="user.cover_path || '/img/default_cover.jpg'"
                        alt="Cover"
                        class="object-cover object-top w-full h-[154px] md:h-[330px] md:rounded-es-lg md:rounded-ee-lg"
                    />

                    <button
                        class="absolute flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded lg:right-5 lg:bottom-40 right-5 top-28 md:top-72 lg:top-auto bg-gray-50 hover:bg-gray-200"
                        title="Actualizar foto de portada"
                    >
                        <CameraIcon class="w-5 h-5 lg:mr-1" />

                        <span class="hidden lg:block mt-[2px]"
                            >Actualizar foto de portada</span
                        >
                    </button>

                    <div
                        class="flex flex-col items-center justify-between bg-white lg:flex-row"
                    >
                        <div class="flex flex-col w-full lg:block">
                            <div
                                class="flex justify-center lg:justify-start lg:static"
                            >
                                <img
                                    :src="
                                        user.avatar_path ||
                                        '/img/default_avatar.png'
                                    "
                                    alt=""
                                    class="absolute top-20 md:top-64 lg:top-auto lg:left-[47px] lg:bottom-3 w-[158px] h-[158px] rounded-full border-4 border-white"
                                />

                                <button
                                    class="absolute flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full right-[136px] md:right-[314px] lg:right-auto lg:left-[156px] top-48 md:top-[369px] lg:top-auto lg:bottom-5 hover:bg-gray-300"
                                    title="Actualizar foto de usuario"
                                >
                                    <CameraIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <div
                                class="lg:pl-[220px] pt-[18px] mb-4 lg:mb-9 flex flex-col items-center lg:items-start lg:mt-0 mt-[69px]"
                            >
                                <h1 class="text-3xl font-extrabold">
                                    {{ user.name }}
                                </h1>
                                <small class="font-bold text-gray-600"
                                    >69 seguidores</small
                                >
                                <div
                                    class="lg:relative flex justify-center lg:justify-start w-full mt-1.5 lg:mb-6 gap-[1px] lg:gap-0"
                                >
                                    <button>
                                        <img
                                            src="/img/demo/avatar.png"
                                            alt=""
                                            class="lg:absolute z-30 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white"
                                        />
                                    </button>
                                    <button>
                                        <img
                                            src="/img/demo/avatar.png"
                                            alt=""
                                            class="lg:absolute z-20 lg:left-6 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white"
                                        />
                                    </button>
                                    <button>
                                        <img
                                            src="/img/demo/avatar.png"
                                            alt=""
                                            class="lg:absolute z-10 lg:left-12 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="isMyProfile"
                            class="flex items-end h-full mt-0 mb-4 lg:mt-11 lg:mb-0"
                        >
                            <PrimaryButton
                                class="lg:mr-[47px] bg-cyan-600 hover:bg-cyan-500"
                                title="Editar"
                            >
                                <PencilSquareIcon class="w-5 h-5 mr-1" />

                                Editar
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="mx-8 border-b-2 border-gray-100"></div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="mt-0">
                <TabGroup>
                    <div class="bg-white shadow">
                        <TabList class="flex mx-auto lg:px-8 lg:w-2/3">
                            <Tab as="template" v-slot="{ selected }">
                                <TabItem
                                    text="Publicaciones"
                                    :selected="selected"
                                />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem
                                    text="Acerca de"
                                    :selected="selected"
                                />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem
                                    text="Seguidores"
                                    :selected="selected"
                                />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Seguidos" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Fotos" :selected="selected" />
                            </Tab>
                        </TabList>
                    </div>

                    <TabPanels class="px-4 mx-auto my-4 lg:px-0 lg:w-2/3">
                        <TabPanel :key="posts" class="p-3 bg-white shadow">
                            Contenido de Publicaciones
                        </TabPanel>

                        <TabPanel :key="followers" class="">
                            <Edit
                                v-if="isMyProfile"
                                :mustVerifyEmail="mustVerifyEmail"
                                :status="status"
                            />
                            <Show v-else :user="user" />
                        </TabPanel>

                        <TabPanel :key="followers" class="p-3 bg-white shadow">
                            Contenido de Seguidores
                        </TabPanel>

                        <TabPanel :key="followers" class="p-3 bg-white shadow">
                            Contenido de Seguidos
                        </TabPanel>

                        <TabPanel :key="followers" class="p-3 bg-white shadow">
                            Contenido de Fotos
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
