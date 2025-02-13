<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import Show from "@/Pages/Profile/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NotificationBox from "@/Components/dearbook/NotificationBox.vue";
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon, PlusIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import PostCreate from "@/Components/dearbook/Post/Create.vue"
import PostList from "@/Components/dearbook/Post/List.vue"
import FollowerList from '@/Pages/Profile/FollowerList.vue'
import FollowingList from '@/Pages/Profile/FollowingList.vue'
import FollowButton from '@/Components/FollowFromProfileButton.vue'
import PhotoList from '@/Pages/Media/PhotoList.vue'

const props = defineProps({
    success: {
        type: [String, Object, null],
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
    groupsOwned: {
        type: Array,
    },
    groupsJoined: {
        type: Array,
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
    parent_page_name: String,
});

const authUser = usePage().props.auth.user
const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const maxFollowersIconsToList = 5

const zIndex = ref(20)
const loadZIndex = (index) => {
    return {
        0: 'z-[20]',
        1: 'z-[19]',
        2: 'z-[18]',
        3: 'z-[17]',
        4: 'z-[16]',
        '+': 'z-[15]',
    }[index];
}

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

const totalOfFollowersText = computed(() => {
    let txt = ''
    if (props.totalOfFollowers === 1) {
        txt = props.totalOfFollowers + " seguidor"
    } else {
        txt = props.totalOfFollowers + " seguidores"
    }
    return txt
})

const successMessage = computed(() => props.success?.message ? props.success.message : props.success)
</script>

<template>

    <Head :title="$t('Profile')" />

    <AuthenticatedLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="lg:w-2/3 mx-auto relative">
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-show="showNotification && success" :title="'Info'" :message="successMessage" />
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-if="showNotification && errors.cover" :title="'Error'" :message="errors.cover" />
                <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                    @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                    v-else-if="showNotification && errors.avatar" :title="'Error'" :message="errors.avatar" />

                <div class="relative bg-white dark:bg-gray-800">
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

                    <div
                        class="flex flex-col items-center justify-between dark:text-white bg-white dark:bg-gray-800 lg:flex-row">
                        <div class="flex flex-col w-full lg:block">
                            <div class="flex justify-center lg:justify-start lg:static">
                                <div
                                    class="absolute p-1 bg-white dark:bg-gray-800 rounded-full top-20 md:top-64 lg:top-auto lg:left-7 lg:bottom-4">
                                    <img :src="avatarImageSrc || user.avatar_url" alt="Imagen de perfil"
                                        class="rounded-full border-[1px] border-gray-300 dark:border-gray-200 bg-gray-50 dark:bg-gray-400 w-[174px] h-[168px]" />
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
                                <small v-if="totalOfFollowers === 0" class="font-bold text-gray-600">{{
                                    totalOfFollowersText }}</small>
                                <button v-else @click="asignSelectedIndex(2)"
                                    class="hover:underline leading-[18px] dark:decoration-gray-400"
                                    title="Listar seguidor(es)">
                                    <small class="font-bold text-gray-600 dark:text-gray-400">{{ totalOfFollowersText
                                        }}</small>
                                </button>

                                <!-- Listando algunos seguidores... -->
                                <div class="relative mt-2.5 lg:mb-6">
                                    <div v-if="followers.length === 0" class="h-[30px]" />
                                    <div v-else
                                        class="flex justify-center -space-x-1 font-mono text-sm font-bold leading-6 text-white lg:justify-start">
                                        <div v-for="(follower, index) of followers?.slice(
                                            0,
                                            maxFollowersIconsToList
                                        )" :class="loadZIndex(index)"
                                            class="flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                                            <a :href="route('profile.index', { username: follower.username })" :title="$t('Profile of', {
                                                'name': follower.name
                                            })">
                                                <img :src="follower.avatar_url" :alt="follower.name"
                                                    class="w-[30px] h-[30px] rounded-full ring-2 ring-white dark:ring-slate-900 bg-gray-100 dark:bg-gray-200 hover:ring-[#0099ce]" />
                                            </a>
                                        </div>
                                        <div v-if="followers.length > maxFollowersIconsToList" :class="loadZIndex('+')"
                                            class="flex items-center justify-center w-[30px] h-[30px] bg-cyan-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            <a href="void 0" @click="asignSelectedIndex(2)"
                                                :title="$t('dearbook.follower.index.general_info.see_more_followers') + ' :: (+' + (followers.length - maxFollowersIconsToList) + ')'">
                                                <PlusIcon class="w-5 h-5" />
                                            </a>
                                        </div>
                                    </div>
                                    <!-- <div v-else
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
                                    </div> -->
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-end h-full mt-0 mb-4 lg:mt-16 lg:mb-0">
                            <div v-if="!isMyProfile" class="lg:mr-[47px]">
                                <FollowButton :user="user" :is-current-user-follower="isCurrentUserFollower" />
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
                    <div class="bg-white dark:bg-gray-800 shadow dark:shadow-gray-600 sticky top-[57px] z-40">
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
                                :after_comment_deleted="after_comment_deleted" :parent_page_name="parent_page_name" />
                            <div v-else
                                class="p-4 mx-0.5 bg-white dark:bg-gray-800 dark:text-gray-100 mt-4 rounded shadow text-center">
                                No hay publicaciones actualmente
                            </div>
                        </TabPanel>

                        <!-- Acerca de -->
                        <TabPanel>
                            <Edit v-if="isMyProfile" :mustVerifyEmail="mustVerifyEmail" :status="status"
                                :groups-owned="groupsOwned" :groups-joined="groupsJoined" />
                            <Show v-else :user="user" :groups-owned="groupsOwned" :groups-joined="groupsJoined" />
                        </TabPanel>

                        <!-- Followers -->
                        <TabPanel class="p-3 bg-white dark:bg-gray-800 dark:text-gray-100 shadow md:w-4/6 mx-auto">
                            <FollowerList :followers="followers" />
                        </TabPanel>

                        <!-- Followings -->
                        <TabPanel class="p-3 bg-white dark:bg-gray-800 dark:text-gray-100 shadow md:w-4/6 mx-auto">
                            <FollowingList :followings="followings" />
                        </TabPanel>

                        <!-- Fotos -->
                        <TabPanel class="p-3 bg-white dark:bg-gray-800 dark:text-gray-100 shadow rounded-md">
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
