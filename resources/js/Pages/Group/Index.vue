<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Group/Edit.vue";
import Show from "@/Pages/Group/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NotificationBox from "@/Components/dearbook/NotificationBox.vue";
import PublicAccessIcon from '@/Components/Icons/PublicAccess.vue'
import PrivateAccessIcon from '@/Components/Icons/PrivateAccess.vue'
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon, EyeIcon, EyeSlashIcon, UserPlusIcon, UserGroupIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    errors: Object,
    success: {
        type: String,
    },
    // status: {
    //     type: String,
    // },
    group: {
        type: Object,
    },
});

const authUser = usePage().props.auth.user;

// const isMyGroupProfile = computed(() => authUser && authUser.id === props.group.user.id);
// o
const isAdminGroup = computed(() => props.group.role === 'admin');
const isUserGroup = computed(() => props.group.role === 'user')
const isNotMemberAndGroupAutoApproval = computed(() => !props.group.role && props.group.auto_approval)
const isNotMemberAndGroupNotAutoApproval = computed(() => !props.group.role && !props.group.auto_approval)
const isPrivateGroup = computed(() => props.group.type === 'private');

// const groupNameTopLength = 52
// const extraName = ' fadsfa sdf fasdfasd fasdf asdfgh'
// const isGroupNameLengthGreaterThanTop = (groupName) => (groupName.length + extraName.length) > groupNameTopLength

const imagesForm = useForm({
    group_id: props.group.id,
    cover: null,
})

const coverImageSrc = ref("");
const thumbnailImageSrc = ref("");

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const timer = ref(null)
const timeOnSeconds = ref(0)
const maxTimeOnSecondsForNotificationBox = usePage().props.maxTimeOnSecondsForNotificationBox;

const activeShowNotification = () => {
    showNotification.value = true

    // setTimeout(() => {
    //     closingNotification('notification')
    // }, 3000)
    startClosingNotification()
}

const startClosingNotification = () => {
    timer.value = setInterval(() => {
        // if (notificationBoxRef.value) {
        //     timeOnSeconds.value++
        //     console.log('timeOnSeconds', timeOnSeconds.value)

        //     if (timeOnSeconds.value >= maxTimeOnSecondsForNotificationBox) {
        //         stopClosingNotification()
        //         closingNotification('notification')
        //         console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        //     }
        // }
        if (notificationBoxRef.value && timeOnSeconds.value < maxTimeOnSecondsForNotificationBox) {
            timeOnSeconds.value++
            console.log('timeOnSeconds', timeOnSeconds.value)
        } else {
            stopClosingNotification()
            closingNotification('notification')
            console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        }
    }, 1000)
}

const stopClosingNotification = () => {
    clearInterval(timer.value)
}

const closingNotification = (className) => {
    timer.value = null
    timeOnSeconds.value = 0
    // notificationBoxRef.value.fadeOutEffect(className)
    if (notificationBoxRef.value) {
        notificationBoxRef.value.fadeOutEffect(className)
    }
}

const closeShowNotification = () => {
    showNotification.value = false
}

const onCoverChange = (event) => {
    console.log(event);

    imagesForm.cover = event.target.files[0];
    if (imagesForm.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            console.log("OnLoad COVER ...");
            coverImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.cover);
    }
};

const resetCoverImage = () => {
    coverImageSrc.value = ''
    imagesForm.cover = null
}

const submitCoverImage = () => {
    console.log(imagesForm.cover)

    var fadeTarget = document.getElementById("notification-box");
    fadeTarget.style.removeProperty("opacity");

    imagesForm.post(route('group.update-cover-image'), {
        onSuccess: () => {
            showNotification.value = true
            resetCoverImage()
        },
        onError: () => {
            showNotification.value = true
        },
        onFinish: () => {
            // setTimeout(() => {
            //     closingNotification('notification')
            // }, 3000)
            startClosingNotification()
        }
    })
}

// -----------------------------------------------

import Modal from '@/Components/Modal.vue';
import CropperIndex from '@/Pages/Cropper/Group/Index.vue'

const showingCropImageModal = ref(false);
const showCropImageModal = () => {
    showingCropImageModal.value = true;
};
const closeCropImageModal = () => {
    showingCropImageModal.value = false;
};
</script>

<template>

    <Head :title="group.name" />

    <AuthenticatedLayout>
        <div class="bg-white">
            <div class="lg:w-2/3 mx-auto relative">
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-show="showNotification && success" :title="'Info'" :message="success" />
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-if="showNotification && errors.cover" :title="'Error'" :message="errors.cover" />
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-else-if="showNotification && errors.thumbnail" :title="'Error'" :message="errors.thumbnail" />

                <div class="relative bg-white">
                    <div class="relative">
                        <div
                            class="w-full py-3.5 md:py-4 md:rounded-es-lg md:rounded-ee-lg bg-cyan-600 text-white absolute bottom-0">
                            <div class="pl-4 md:hidden text-xs leading-tight">
                                Grupo de
                                <span class="font-bold">
                                    <a :href="route('profile.index', { username: group.user.username })"
                                        class="hover:underline" :title="'Perfil de ' + group.user.name">{{
                                            group.user.name }}</a>
                                </span>
                            </div>
                            <div class="hidden md:block md:pl-[228px]">
                                Grupo de
                                <span class="font-bold">
                                    <a :href="route('profile.index', { username: group.user.username })"
                                        class="hover:underline" :title="'Perfil de ' + group.user.name">{{
                                            group.user.name }}</a>
                                </span>
                            </div>
                        </div>
                        <img :src="coverImageSrc || group.cover_url || '/img/default_cover_group.jpg'" alt="Cover"
                            class="object-cover object-top w-full h-[154px] md:h-[330px] md:rounded-es-lg md:rounded-ee-lg" />
                    </div>

                    <div class="absolute lg:right-5 lg:bottom-[172px] right-4 top-[120px] md:top-72">
                        <button v-if="!coverImageSrc"
                            class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                            title="Actualizar imagen de portada">
                            <CameraIcon class="w-5 h-5 lg:mr-1" />

                            <span class="hidden lg:block mt-[2px]">Actualizar imagen de portada</span>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onCoverChange"
                                title="Actualizar imagen de portada" />
                        </button>

                        <div v-else class="flex gap-2">
                            <button @click="resetCoverImage"
                                class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                                title="Cancelar">
                                <XMarkIcon class="w-5 h-5 lg:mr-1" />

                                <span class="hidden lg:block mt-[2px]">Cancelar</span>
                            </button>
                            <button @click="submitCoverImage"
                                class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-100 rounded bg-gray-500 hover:bg-gray-600"
                                title="Enviar">
                                <CheckIcon class="w-5 h-5 lg:mr-1" />

                                <span class="hidden lg:block mt-[2px]">Enviar</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row items-center justify-between bg-white px-3 md:px-0">
                        <div class="flex lg:flex-col w-full lg:block mt-4 lg:mt-0 gap-1 lg:gap-0">
                            <div class="">
                                <div
                                    class="md:absolute p-1 bg-white rounded-md md:top-64 lg:top-[295px] lg:left-7 lg:bottom-4">
                                    <img :src="thumbnailImageSrc || group.thumbnail_url ||
                                        '/img/default_thumbnail_group.png'
                                        " alt=""
                                        class="rounded-md border-[1px] border-gray-200 min-w-[106px] lg:min-w-[174px] w-[106px] h-[100px] lg:w-[174px] lg:h-[168px]" />
                                </div>

                                <div
                                    class="absolute left-[85px] md:right-[314px] lg:right-auto lg:left-[164px] top-[235px] md:top-[369px] lg:top-[427px] lg:bottom-7">
                                    <button @click="showCropImageModal"
                                        class="flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300"
                                        title="Actualizar imagen de grupo">
                                        <CameraIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="lg:pl-[228px] lg:pt-[16px] mb-4 lg:mb-0 flex flex-col items-start">
                                <!-- <pre>trerte rt {{ isGroupNameLengthGreaterThanTop(group.name) }}  {{ group.name.length + extraName.length }}</pre> -->
                                <h1 class="text-[28px] md:text-[32px] font-extrabold mt-2 leading-none">
                                    {{ group.name }} {{ extraName }}
                                </h1>

                                <small class="text-gray-600 flex items-center gap-1 mt-2">
                                    <PrivateAccessIcon v-if="isPrivateGroup" class-content="w-3.5 h-3.5"
                                        fill-content="#4b5563" />
                                    <PublicAccessIcon v-else class-content="w-3 h-3" fill-content="#4b5563" /> {{
                                        $t('dearbook.group.info.type.' + group.type) }} · <span class="font-bold">74 {{
                                        $t('dearbook.group.info.members') }}</span>
                                </small>

                                <div class="relative mt-2.5 lg:mb-6">
                                    <div
                                        class="flex justify-center -space-x-1 font-mono text-sm font-bold leading-6 text-white lg:justify-start">
                                        <div class="z-20 flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                                            <a href="#">
                                                <img src="/img/demo/avatar.png" alt=""
                                                    class="w-[30px] h-[30px] rounded-full ring-2 ring-white dark:ring-slate-900" />
                                            </a>
                                        </div>
                                        <div
                                            class="z-[19] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            04</div>
                                        <div
                                            class="z-[18] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            03</div>
                                        <div
                                            class="z-[17] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            02</div>
                                        <div
                                            class="z-[16] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            01</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-end h-full mt-0 mb-4 lg:mt-16 lg:mb-0 lg:mr-[47px]">
                            <button v-if="isAdminGroup"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Invitar usuarios">
                                <UserPlusIcon class="w-5 h-5 mr-1" />
                                Invitar
                            </button>
                            <button v-if="isNotMemberAndGroupAutoApproval"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Unirte al grupo">
                                <UserGroupIcon class="w-5 h-5 mr-1" />
                                Unirte al grupo
                            </button>
                            <button v-if="isNotMemberAndGroupNotAutoApproval"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Solicitar unirte al grupo">
                                <UserGroupIcon class="w-5 h-5 mr-1" />
                                Solicitar unirte al grupo
                            </button>
                            <button v-if="isUserGroup"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Detalles de Miembro">
                                <UserGroupIcon class="w-5 h-5 mr-1" />
                                Detalles de Miembro
                            </button>
                            <PrimaryButton v-if="isAdminGroup" class="bg-cyan-600 hover:bg-cyan-500" title="Editar">
                                <PencilSquareIcon class="w-5 h-5 mr-1" />
                                Editar
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="mx-8 border-b-2 border-gray-100 lg:mt-2"></div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="mt-0">
                <TabGroup>
                    <div class="bg-white shadow sticky top-[57px]">
                        <TabList class="flex mx-auto lg:px-8 lg:w-2/3">
                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Conversación" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }">
                                <TabItem text="Información" :selected="selected" />
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

                    <TabPanels class="px-4 mx-auto my-4 lg:px-0 lg:w-2/3">
                        <TabPanel :key="posts" class="p-3 bg-white shadow">
                            Contenido de Conversación
                        </TabPanel>

                        <TabPanel :key="followers" class="">
                            <Edit v-if="isAdminGroup" :status="status" />
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

        <Modal :show="showingCropImageModal" @close="closeCropImageModal">
            <CropperIndex @callCloseCropImageModal="closeCropImageModal"
                @callActiveShowNotification="activeShowNotification" :group="group" />
        </Modal>
    </AuthenticatedLayout>
</template>
