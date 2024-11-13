<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import Show from "@/Pages/Profile/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NotificationBox from "@/Components/dearbook/NotificationBox.vue";
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon, UserMinusIcon, UserPlusIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import PostCreate from "@/Components/dearbook/Post/Create.vue"
import PostList from "@/Components/dearbook/Post/List.vue"
import FollowerList from '@/Pages/Profile/FollowerList.vue'
import FollowingList from '@/Pages/Profile/FollowingList.vue'
import PhotoList from '@/Pages/Media/PhotoList.vue'

const props = defineProps({
    success: {
        type: String,
    },
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    },
    posts: {
        type: Object,
    },
    after_comment_deleted: {
        type: Object,
    },
    defaultIndex: {
        type: Number,
        default: 0,
    },
    isCurrentUserFollower: Boolean,
    totalOfFollowers: Number,
    followers: {
        type: Array,
    },
    followings: {
        type: Array,
    },
    photos: {
        type: Object,// Array // Object,
    },
});

const authUser = usePage().props.auth.user
const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

// const theSelectedIndex = ref(0)
const theSelectedIndex = ref(props.defaultIndex)
const getSetSelectedIndex = computed({
    get() {
        return theSelectedIndex.value
    },
    set(newIndex) {
        theSelectedIndex.value = newIndex
    }
})
const asignSelectedIndex = (index) => {
    getSetSelectedIndex.value = index
}

const imagesForm = useForm({
    cover: null,
})

const coverImageSrc = ref("");
const avatarImageSrc = ref("");

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

    imagesForm.post(route('profile.update-cover-image'), {
        preserveScroll: true,
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
import CropperIndex from '@/Pages/Cropper/User/Index.vue'

const showingCropImageModal = ref(false);
const showCropImageModal = () => {
    showingCropImageModal.value = true;
};
const closeCropImageModal = () => {
    showingCropImageModal.value = false;
};

const followUnfollow = () => {
    const form = useForm({
        follow: !props.isCurrentUserFollower
    })

    form.post(route('user.follow-unfollow', props.user.id), {
        preserveScroll: true,
    })
}

const totalOfFollowersText = computed(() => {
    let txt = ''
    if (props.totalOfFollowers === 1) {
        txt = props.totalOfFollowers + " seguidor"
    } else {
        txt = props.totalOfFollowers + " seguidores"
    }
    return txt
})
</script>

<template>

    <Head :title="$t('Profile')" />

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
                    v-else-if="showNotification && errors.avatar" :title="'Error'" :message="errors.avatar" />

                <div class="relative bg-white">
                    <img :src="coverImageSrc || user.cover_url" alt="Cover"
                        class="object-cover object-top w-full h-[154px] md:h-[330px] md:rounded-es-lg md:rounded-ee-lg" />

                    <div class="absolute lg:right-5 lg:bottom-[172px] right-5 top-28 md:top-72 lg:top-auto">
                        <button v-if="!coverImageSrc && isMyProfile"
                            class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                            title="Actualizar foto de portada">
                            <CameraIcon class="w-5 h-5 lg:mr-1" />

                            <span class="hidden lg:block mt-[2px]">Actualizar foto de portada</span>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onCoverChange"
                                title="Actualizar foto de portada" />
                        </button>

                        <div v-else-if="isMyProfile" class="flex gap-2">
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

                    <div class="flex flex-col items-center justify-between bg-white lg:flex-row">
                        <div class="flex flex-col w-full lg:block">
                            <div class="flex justify-center lg:justify-start lg:static">
                                <div
                                    class="absolute p-1 bg-white rounded-full top-20 md:top-64 lg:top-auto lg:left-7 lg:bottom-4">
                                    <img :src="avatarImageSrc || user.avatar_url" alt=""
                                        class="rounded-full border-[1px] border-gray-200 w-[174px] h-[168px]" />
                                </div>

                                <div
                                    class="absolute right-[135px] md:right-[314px] lg:right-auto lg:left-[164px] top-[200px] md:top-[369px] lg:top-auto lg:bottom-7">
                                    <button v-if="isMyProfile" @click="showCropImageModal"
                                        class="flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300"
                                        title="Actualizar foto de perfil">
                                        <CameraIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div
                                class="lg:pl-[228px] pt-[16px] mb-4 lg:mb-0 flex flex-col items-center lg:items-start lg:mt-0 mt-20">
                                <h1 class="text-[32px] font-extrabold">
                                    {{ user.name }}
                                </h1>
                                <small v-if="totalOfFollowers === 0" class="font-bold text-gray-600">{{ totalOfFollowersText }}</small>
                                <button v-else @click="asignSelectedIndex(2)" class="hover:underline leading-[18px]"
                                    title="Listar seguidor(es)">
                                    <small class="font-bold text-gray-600">{{ totalOfFollowersText }}</small>
                                </button>

                                <div class="relative mt-2.5 lg:mb-6">
                                    <div
                                        class="flex justify-center -space-x-1 font-mono text-sm font-bold leading-6 text-white lg:justify-start">
                                        <div class="z-20 flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                                            <a href="#">
                                                <img src="/img/default_avatar.png" alt=""
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

                        <div class="flex gap-2 items-end h-full mt-0 mb-4 lg:mt-16 lg:mb-0">
                            <div v-if="!isMyProfile" class="lg:mr-[47px]">
                                <button v-if="!isCurrentUserFollower" @click="followUnfollow"
                                    class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150" title="Seguir a este usuario">
                                    <UserPlusIcon class="w-5 h-5 md:mr-1" />
                                    <span class="hidden md:block">Seguir</span>
                                </button>
                                <button v-else @click="followUnfollow"
                                    class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-red-300 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-300 uppercase tracking-widest hover:bg-red-800 dark:hover:bg-white focus:bg-red-800 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150" title="Dejar de seguir a este usuario">
                                    <UserMinusIcon class="w-5 h-5 md:mr-1" />
                                    <span class="hidden md:block">No Seguir</span>
                                </button>
                            </div>

                            <!-- <div v-if="isMyProfile" class="w-0.5 h-9 bg-[#0099ce]" /> -->

                            <PrimaryButton v-if="isMyProfile" @click="asignSelectedIndex(1)"
                                class="lg:mr-[47px] bg-cyan-600 hover:bg-cyan-500" title="Editar">
                                <PencilSquareIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Editar</span>
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="mx-8 border-b-2 border-gray-100 lg:mt-2"></div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="mt-0">
                <TabGroup :selectedIndex="getSetSelectedIndex">
                    <div class="bg-white shadow sticky top-[57px] z-40">
                        <TabList class="flex mx-auto lg:px-8 lg:w-2/3">
                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(0)">
                                <TabItem text="Publicaciones" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(1)">
                                <TabItem text="Acerca de" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(2)">
                                <TabItem text="Seguidores" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(3)">
                                <TabItem text="Seguidos" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(4)">
                                <TabItem text="Fotos" :selected="selected" />
                            </Tab>
                        </TabList>
                    </div>

                    <TabPanels class="px-4 mx-auto my-4 lg:px-0 lg:w-2/3">
                        <!-- Publicaciones -->
                        <TabPanel>
                            <PostCreate />
                            <PostList v-if="posts.data.length > 0" class="flex-1 last:mb-[5px]" :posts="posts.data"
                                :after_comment_deleted="after_comment_deleted" />
                            <div v-else class="p-4 mx-0.5 bg-white mt-4 rounded shadow text-center">
                                No hay publicaciones actualmente
                            </div>
                        </TabPanel>

                        <!-- Acerca de -->
                        <TabPanel>
                            <Edit v-if="isMyProfile" :mustVerifyEmail="mustVerifyEmail" :status="status" />
                            <Show v-else :user="user" />
                        </TabPanel>

                        <!-- Followers -->
                        <TabPanel class="p-3 bg-white shadow md:w-4/6 mx-auto">
                            <FollowerList :followers="followers" />
                        </TabPanel>

                        <!-- Followings -->
                        <TabPanel class="p-3 bg-white shadow md:w-4/6 mx-auto">
                            <FollowingList :followings="followings" />
                        </TabPanel>

                        <!-- Fotos -->
                        <TabPanel class="p-3 bg-white shadow rounded-md">
                            <PhotoList :photos="photos" />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>

        <Modal :show="showingCropImageModal" @close="closeCropImageModal">
            <CropperIndex @callCloseCropImageModal="closeCropImageModal"
                @callActiveShowNotification="activeShowNotification" :user="user" />
        </Modal>
    </AuthenticatedLayout>
</template>
