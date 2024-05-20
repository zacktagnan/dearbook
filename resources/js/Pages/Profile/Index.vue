<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Edit from '@/Pages/Profile/Edit.vue'
import Show from '@/Pages/Profile/Show.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import TabItem from '@/Pages/Profile/Partials/TabItem.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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
            <div class="w-2/3 mx-auto pt-[58px]">
                <div class="relative bg-white">
                    <img src="/img/demo/cover.jpg" alt="Cover"
                        class="object-cover object-top w-full h-[330px] rounded-es-lg rounded-ee-lg">

                    <div class="flex items-center justify-between bg-white ">
                        <div>
                            <img src="/img/demo/avatar.png" alt=""
                                class="absolute left-[47px] bottom-3 w-[158px] h-[158px] rounded-full border-4 border-white">
                            <div class="pl-[220px] pt-[18px] pb-9 flex flex-col">
                                <h1 class="text-3xl font-extrabold">{{ user.name }}</h1>
                                <small class="font-bold text-gray-600">69 seguidores</small>
                                <div class="relative flex w-full mt-1.5 mb-6">
                                    <img src="/img/demo/avatar.png" alt=""
                                        class="absolute z-30 w-[33px] h-[33px] rounded-full border-2 border-white">
                                    <img src="/img/demo/avatar.png" alt=""
                                        class="absolute z-20 left-6 w-[33px] h-[33px] rounded-full border-2 border-white">
                                    <img src="/img/demo/avatar.png" alt=""
                                        class="absolute z-10 left-12 w-[33px] h-[33px] rounded-full border-2 border-white">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-end h-full mt-11">
                            <PrimaryButton v-if="isMyProfile" class="mr-[47px] bg-cyan-600 hover:bg-cyan-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>

                                Editar
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="mx-8 border-b-2 border-gray-100 ">

                    </div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="mt-0">
                <TabGroup>
                    <div class="w-full bg-white shadow">
                        <TabList class="flex w-2/3 px-8 mx-auto">
                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Publicaciones" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Acerca de" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Seguidores" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Seguidos" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Fotos" :selected="selected" />
                            </Tab>
                        </TabList>
                    </div>

                    <TabPanels class="w-2/3 mx-auto my-4">
                        <TabPanel :key="posts" class="p-3 bg-white shadow">
                            Contenido de Publicaciones
                        </TabPanel>

                        <TabPanel :key="followers" class="">
                            <Edit v-if="isMyProfile" :mustVerifyEmail="mustVerifyEmail" :status="status" />
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
