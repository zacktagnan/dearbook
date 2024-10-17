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
import PendingRequestIcon from '@/Components/Icons/PendingRequest.vue'
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon, PlusIcon, UserPlusIcon, UserGroupIcon, ArrowLeftStartOnRectangleIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import TextInput from '@/Components/TextInput.vue'
import UserItem from '@/Components/dearbook/User/Item.vue'
import PostCreate from "@/Components/dearbook/Post/Create.vue"
import PostList from "@/Components/dearbook/Post/List.vue"

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
    requestsPending: Array,
});

const authUser = usePage().props.auth.user;
// const isMyGroupProfile = computed(() => authUser && authUser.id === props.group.user.id);
// o
const authUserIsTheOwnerGroup = computed(() => authUser && authUser.id === props.group.user.id)
const isTheOwnerGroup = (memberId) => props.group.user.id === memberId

const isAdminGroup = computed(() => props.group.role === 'admin');
// const isUserGroup = computed(() => props.group.role === 'user' && props.group.status === 'approved')
const isMemberGroup = computed(() => props.group.role && props.group.status === 'approved')
const isUserGroupPending = computed(() => props.group.role === 'user' && props.group.status === 'pending')
const isUserGroupRejected = computed(() => props.group.role === 'user' && props.group.status === 'rejected')
const isNotMemberAndGroupAutoApproval = computed(() => !props.group.role && props.group.auto_approval)
const isNotMemberAndGroupNotAutoApproval = computed(() => !props.group.role && !props.group.auto_approval)
const isPrivateGroup = computed(() => props.group.type === 'private')
const isAutoApprovalGroup = computed(() => props.group.auto_approval);

const maxGroupUsersIconsToList = 5

const searchKeyword = ref('')

const zIndex = ref(20)
// const loadZIndex = () => {
//     --zIndex.value
//     console.log('devuelto:', 'z-' + zIndex.value)
//     return 'z-' + zIndex.value
// }
// ---------------------------------------------------------
// const loadZIndex = (index) => 'z-[' + (20 - index) + ']'
// const loadZIndex = (index) => 20 - index
// ---------------------------------------------------------
// const loadZIndex = (index) => {
//     zIndex.value = zIndex.value - index
//     return 'z-[' + zIndex.value + ']'
// }
// Mejor manera de plasmar el z-index dinámicamente según el index recibido
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

    startClosingNotification()
}

const startClosingNotification = () => {
    timer.value = setInterval(() => {
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
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true
            resetCoverImage()
        },
        onError: () => {
            showNotification.value = true
        },
        onFinish: () => {
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

// -----------------------------------------------

import InviteUserModal from '@/Pages/Group/InviteUserModal.vue'

const showingInviteUserModal = ref(false);
const showInviteUserModal = () => {
    showingInviteUserModal.value = true;
};

const joinToGroup = () => {
    const joinForm = useForm({})

    joinForm.post(route('group.join', props.group.slug), {
        preserveScroll: true
    })
}

const requestJoinToGroup = () => {
    const joinForm = useForm({})

    joinForm.post(route('group.request-join', props.group.slug), {
        preserveScroll: true
    })
}

const approveUser = (user) => {
    const form = useForm({
        user_id: user.id,
        action: 'approved',
    })

    form.post(route('group.request-approve-or-not', props.group.slug), {
        preserveScroll: true
    })
}

const rejectUser = (user) => {
    const form = useForm({
        user_id: user.id,
        action: 'rejected',
    })

    form.post(route('group.request-approve-or-not', props.group.slug), {
        preserveScroll: true
    })
}

const memberRoleChange = (user, newRoleSelected) => {
    console.log(user, newRoleSelected)
    const form = useForm({
        user_id: user.id,
        role: newRoleSelected,
    })

    form.post(route('group.change-role', props.group.slug), {
        preserveScroll: true
    })
}
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
                                {{ $t('dearbook.group.general_info.group_by') }}
                                <span class="font-bold">
                                    <a :href="route('profile.index', { username: group.user.username })"
                                        class="hover:underline" :title="$t('Profile of', {
                                            'name': group.user.name
                                        })">{{
                                            group.user.name }}</a>
                                </span>
                            </div>
                            <div class="hidden md:block md:pl-[228px]">
                                {{ $t('dearbook.group.general_info.group_by') }}
                                <span class="font-bold">
                                    <a :href="route('profile.index', { username: group.user.username })"
                                        class="hover:underline" :title="$t('Profile of', {
                                            'name': group.user.name
                                        })">{{
                                            group.user.name }}</a>
                                </span>
                            </div>
                        </div>
                        <img :src="coverImageSrc || group.cover_url" alt="Cover"
                            class="object-cover object-top w-full h-[154px] md:h-[330px] md:rounded-es-lg md:rounded-ee-lg" />
                    </div>

                    <div class="absolute lg:right-5 lg:bottom-[172px] right-4 top-[120px] md:top-72">
                        <button v-if="!coverImageSrc && isAdminGroup"
                            class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                            :title="$t('dearbook.profile_images.group.cover.update_cover_image')">
                            <CameraIcon class="w-5 h-5 lg:mr-1" />

                            <span class="hidden lg:block mt-[2px]">{{
                                $t('dearbook.profile_images.group.cover.update_cover_image') }}</span>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onCoverChange"
                                :title="$t('dearbook.profile_images.group.cover.update_cover_image')" />
                        </button>

                        <div v-else-if="isAdminGroup" class="flex gap-2">
                            <button @click="resetCoverImage"
                                class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                                :title="$t('Cancel')">
                                <XMarkIcon class="w-5 h-5 lg:mr-1" />

                                <span class="hidden lg:block mt-[2px]">{{ $t('Cancel') }}</span>
                            </button>
                            <button @click="submitCoverImage"
                                class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-100 rounded bg-gray-500 hover:bg-gray-600"
                                :title="$t('Send')">
                                <CheckIcon class="w-5 h-5 lg:mr-1" />

                                <span class="hidden lg:block mt-[2px]">{{ $t('Send') }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row items-center justify-between bg-white px-3 md:px-0">
                        <div class="flex lg:flex-col w-full lg:block mt-4 lg:mt-0 gap-1 lg:gap-0">
                            <div>
                                <div
                                    class="md:absolute p-1 bg-white rounded-md md:top-64 lg:top-[295px] lg:left-7 lg:bottom-4">
                                    <img :src="thumbnailImageSrc || group.thumbnail_url" alt=""
                                        class="rounded-md border-[1px] border-gray-200 min-w-[106px] lg:min-w-[174px] w-[106px] h-[100px] lg:w-[174px] lg:h-[168px]" />
                                </div>

                                <div v-if="isAdminGroup"
                                    class="absolute left-[85px] md:right-[314px] lg:right-auto lg:left-[164px] top-[235px] md:top-[369px] lg:top-[427px] lg:bottom-7">
                                    <button @click="showCropImageModal"
                                        class="flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300"
                                        :title="$t('dearbook.profile_images.group.thumbnail.update_thumbnail_image')">
                                        <CameraIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="lg:pl-[228px] lg:pt-[16px] mb-4 lg:mb-0 flex flex-col items-start">
                                <h1 class="text-[28px] md:text-[32px] font-extrabold mt-2 leading-none">
                                    {{ group.name }}
                                </h1>

                                <small class="text-gray-600 flex items-center gap-1 mt-2">
                                    <PrivateAccessIcon v-if="isPrivateGroup" class-content="w-3.5 h-3.5"
                                        fill-content="#4b5563" />
                                    <PublicAccessIcon v-else class-content="w-3 h-3" fill-content="#4b5563" /> {{
                                        $t('dearbook.group.general_info.type.' + group.type) }} · <span
                                        v-if="group.total_group_user === 0" class="font-bold">{{
                                            $tChoice('dearbook.group.general_info.x_members', group.total_group_user, {
                                                'total': group.total_group_user
                                            }) }}</span><button v-else-if="isMemberGroup || !isPrivateGroup"
                                        @click="asignSelectedIndex(2)" class="hover:underline"
                                        :title="$tChoice('dearbook.group.general_info.title_list_members', group.total_group_user)">
                                        <span class="font-bold">{{
                                            $tChoice('dearbook.group.general_info.x_members', group.total_group_user, {
                                                'total': group.total_group_user
                                            }) }}</span>
                                    </button><span v-else class="font-bold">{{
                                        $tChoice('dearbook.group.general_info.x_members', group.total_group_user, {
                                            'total': group.total_group_user
                                        }) }}</span>
                                </small>

                                <div class="relative mt-2.5 lg:mb-6">
                                    <div v-if="group.total_group_user === 0" class="h-[30px]" />
                                    <div v-else-if="isMemberGroup || !isPrivateGroup"
                                        class="flex justify-center -space-x-1 font-mono text-sm font-bold leading-6 text-white lg:justify-start">
                                        <div v-for="(groupUser, index) of group.all_group_users?.slice(
                                            0,
                                            maxGroupUsersIconsToList
                                        )" :class="loadZIndex(index)"
                                            class="flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                                            <a :href="route('profile.index', { username: groupUser.username })" :title="$t('Profile of', {
                                                'name': groupUser.name
                                            })">
                                                <img :src="groupUser.avatar_url" :alt="groupUser.name"
                                                    class="w-[30px] h-[30px] rounded-full ring-2 ring-white dark:ring-slate-900 bg-white hover:ring-[#0099ce]" />
                                            </a>
                                        </div>
                                        <div v-if="group.total_group_user > maxGroupUsersIconsToList"
                                            :class="loadZIndex('+')"
                                            class="flex items-center justify-center w-[30px] h-[30px] bg-cyan-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            <a href="#"
                                                :title="$t('dearbook.group.general_info.see_more_members') + ' :: (+' + (group.total_group_user - maxGroupUsersIconsToList) + ')'">
                                                <PlusIcon class="w-5 h-5" />
                                            </a>
                                        </div>
                                        <!-- Muestra de iconos de usuario de z-20 a z-16 -->
                                        <!-- <div
                                            class="z-[18] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            03</div>
                                        <div
                                            class="z-[17] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            02</div>
                                        <div
                                            class="z-[16] flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            01</div> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-end h-full mt-0 mb-4 lg:mt-16 lg:mb-0 lg:mr-[47px]">
                            <button v-if="isNotMemberAndGroupAutoApproval" @click="joinToGroup"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Unirte al grupo">
                                <UserGroupIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Unirte al grupo</span>
                            </button>
                            <button v-if="isNotMemberAndGroupNotAutoApproval" @click="requestJoinToGroup"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Solicitar unirte al grupo">
                                <UserGroupIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Solicitar unirte al grupo</span>
                            </button>
                            <div v-if="isUserGroupPending"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest transition ease-in-out duration-150"
                                title="Solicitud Pendiente de Aprobación">
                                <PendingRequestIcon class-content="w-5 h-5 md:mr-1" fill-content="#ffffff" />
                                <span class="hidden md:block">Solicitud Pendiente de Aprobación</span>
                            </div>
                            <div v-if="isUserGroupRejected"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-gray-400 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-400 uppercase tracking-widest transition ease-in-out duration-150"
                                title="Solicitud Rechazada">
                                <ArrowLeftStartOnRectangleIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Solicitud Rechazada</span>
                            </div>
                            <button v-if="isMemberGroup"
                                class="inline-flex whitespace-nowrap items-center px-4 py-2 bg-cyan-700 dark:bg-cyan-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-cyan-700 uppercase tracking-widest hover:bg-cyan-600 dark:hover:bg-white focus:bg-cyan-600 dark:focus:bg-white active:bg-cyan-900 dark:active:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-cyan-800 transition ease-in-out duration-150"
                                title="Detalles de Miembro">
                                <UserGroupIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Detalles de Miembro</span>
                            </button>

                            <div v-if="isAdminGroup" class="w-0.5 h-9 bg-[#0099ce]" />

                            <PrimaryButton v-if="isAdminGroup" @click="showInviteUserModal" title="Invitar usuarios">
                                <UserPlusIcon class="w-5 h-5 md:mr-1" />
                                <span class="hidden md:block">Invitar</span>
                            </PrimaryButton>
                            <PrimaryButton v-if="isAdminGroup" @click="asignSelectedIndex(1)"
                                title="Editar perfil de grupo">
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
                    <div class="bg-white shadow sticky top-[57px] z-20">
                        <TabList class="flex mx-auto lg:px-8 lg:w-2/3">
                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(0)">
                                <TabItem text="Conversación" :selected="selected" />
                            </Tab>

                            <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(1)">
                                <TabItem text="Información" :selected="selected" />
                            </Tab>

                            <template v-if="isMemberGroup || !isPrivateGroup">
                                <Tab as="template" v-slot="{ selected }" @click="asignSelectedIndex(2)">
                                    <TabItem text="Miembros" :selected="selected" />
                                </Tab>

                                <Tab as="template" v-if="isAdminGroup && !isAutoApprovalGroup" v-slot="{ selected }"
                                    @click="asignSelectedIndex(3)">
                                    <TabItem text="Solicitudes" :selected="selected" />
                                </Tab>

                                <Tab as="template" v-slot="{ selected }"
                                    @click="isAdminGroup ? asignSelectedIndex(4) : asignSelectedIndex(3)">
                                    <TabItem text="Fotos" :selected="selected" />
                                </Tab>
                            </template>
                        </TabList>
                    </div>

                    <TabPanels class="px-4 mx-auto my-4 lg:px-0 lg:w-2/3">
                        <!-- Conversaciones -->
                        <TabPanel>
                            <template v-if="isMemberGroup || !isPrivateGroup">
                                <template v-if="isMemberGroup">
                                    <PostCreate :group="group" />
                                </template>
                                <PostList v-if="posts.data.length > 0" class="flex-1 last:mb-[5px]" :posts="posts.data"
                                    :after_comment_deleted="after_comment_deleted" />
                                <div v-else class="p-4 mx-0.5 bg-white mt-4 rounded shadow text-center">No hay
                                    conversaciones actualmente</div>
                            </template>
                            <template v-else-if="!isMemberGroup && isPrivateGroup">
                                <div class="p-4 bg-white mt-4 rounded shadow flex justify-center items-center gap-4">
                                    <img src="/img/posts-blocked.png" class="w-20" alt="Apartado bloqueado" />
                                    <div>
                                        <h3 class="font-bold text-lg">Este grupo es <span class="italic">PRIVADO</span>
                                        </h3>
                                        <p class="mt-2">Es preciso unirse al grupo para poder ver las conversaciones o
                                            participar en
                                            ellas.</p>
                                    </div>
                                </div>
                            </template>
                        </TabPanel>

                        <!-- Información -->
                        <TabPanel :key="followers" class="">
                            <Edit v-if="isAdminGroup" @callActiveShowNotification="activeShowNotification"
                                :group="group" />
                            <Show v-else :group="group" />
                        </TabPanel>

                        <template v-if="isMemberGroup || !isPrivateGroup">
                            <!-- Miembros -->
                            <TabPanel class="p-3 bg-white shadow md:w-4/6 mx-auto">
                                <TextInput class="w-full" :model-value="searchKeyword"
                                    :placeholder="$t('dearbook.group.search_inside_profile.placeholder')" />
                                <div v-if="group.all_group_users.length" class="grid gap-3 mt-3">
                                    <UserItem v-for="member of group.all_group_users" :user="member"
                                        :classes="' shadow shadow-gray-200 hover:shadow-gray-400 hover:bg-gray-50'"
                                        :key="member.id">
                                        <div v-if="isAdminGroup">
                                            <select @change="memberRoleChange(member, $event.target.value)"
                                                :disabled="isTheOwnerGroup(member.id)"
                                                class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400">
                                                <option value="admin" :selected="member.role === 'admin'">admin</option>
                                                <option value="user" :selected="member.role === 'user'">user</option>
                                            </select>
                                        </div>
                                    </UserItem>
                                </div>
                                <div v-else>
                                    <p class="w-full text-center">
                                        {{ $t('dearbook.group.list_members.no_registers') }}
                                    </p>
                                </div>
                            </TabPanel>

                            <!-- Solicitudes -->
                            <TabPanel v-if="isAdminGroup && !isAutoApprovalGroup"
                                class="p-3 bg-white shadow md:w-4/6 mx-auto">
                                <div v-if="requestsPending.length" class="grid md:grid-cols-2 gap-3">
                                    <UserItem v-for="user of requestsPending" :user="user"
                                        :classes="' shadow shadow-gray-200 hover:shadow-gray-400 hover:bg-gray-50'"
                                        :key="user.id">
                                        <button @click.prevent.stop="approveUser(user)" title="Aprobar solicitud"
                                            class="rounded-md px-2 py-1 text-gray-500 hover:text-white bg-emerald-200 hover:bg-emerald-400">
                                            <CheckIcon class="w-4 h-4 md:hidden" />
                                            <span class="text-sm hidden md:block">aprobar</span>
                                        </button>
                                        <button @click.prevent.stop="rejectUser(user)" title="Rechazar solicitud"
                                            class="rounded-md px-2 py-1 text-gray-500 hover:text-white bg-red-200 hover:bg-red-400">
                                            <XMarkIcon class="w-4 h-4 md:hidden" />
                                            <span class="text-sm hidden md:block">rechazar</span>
                                        </button>
                                    </UserItem>
                                </div>
                                <div v-else>
                                    <p class="w-full text-center">
                                        {{ $t('dearbook.group.list_requests.no_registers') }}
                                    </p>
                                </div>
                            </TabPanel>

                            <!-- Fotos -->
                            <TabPanel :key="followers" class="p-3 bg-white shadow">
                                Contenido de Fotos
                            </TabPanel>
                        </template>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>

        <Modal :show="showingCropImageModal" @close="closeCropImageModal">
            <CropperIndex @callCloseCropImageModal="closeCropImageModal"
                @callActiveShowNotification="activeShowNotification" :group="group" />
        </Modal>

        <InviteUserModal :group="group" v-model="showingInviteUserModal"
            @callActiveShowNotification="activeShowNotification" />
    </AuthenticatedLayout>
</template>
