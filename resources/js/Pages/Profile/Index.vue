<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import Show from "@/Pages/Profile/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

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
                        src="/img/demo/cover.jpg"
                        alt="Cover"
                        class="object-cover object-top w-full md:h-[330px] md:rounded-es-lg md:rounded-ee-lg"
                    />

                    <button
                        class="absolute flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded lg:right-5 lg:bottom-40 right-5 top-28 md:top-72 lg:top-auto bg-gray-50 hover:bg-gray-200"
                        title="Actualizar foto de portada"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-5 h-5 lg:mr-1"
                        >
                            <path
                                d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z"
                            />
                            <path
                                fill-rule="evenodd"
                                d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <span class="hidden lg:block mt-[2px]"
                            >Actualizar foto de portada</span
                        >
                    </button>

                    <div
                        class="flex flex-col items-center justify-between bg-white lg:flex-row"
                    >
                        <div class="flex flex-col w-full lg:block">
                            <!-- <div class="relative lg:static">
                                <img
                                    src="/img/demo/avatar.png"
                                    alt=""
                                    class="absolute left-[47px] -bottom-20 lg:bottom-3 w-[158px] h-[158px] rounded-full border-4 border-white"
                                />

                                <button
                                    class="absolute flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full left-[156px] top-11 lg:top-auto lg:bottom-5 hover:bg-gray-300"
                                    title="Actualizar foto de usuario"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z"
                                        />
                                        <path
                                            fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div> -->
                            <div
                                class="flex justify-center lg:justify-start lg:static"
                            >
                                <img
                                    src="/img/demo/avatar.png"
                                    alt=""
                                    class="absolute top-20 md:top-64 lg:top-auto lg:left-[47px] lg:bottom-3 w-[158px] h-[158px] rounded-full border-4 border-white"
                                />

                                <button
                                    class="absolute flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full right-[136px] md:right-[314px] lg:right-auto lg:left-[156px] top-48 md:top-[369px] lg:top-auto lg:bottom-5 hover:bg-gray-300"
                                    title="Actualizar foto de usuario"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z"
                                        />
                                        <path
                                            fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
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
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5 mr-1"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                    />
                                </svg>

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
