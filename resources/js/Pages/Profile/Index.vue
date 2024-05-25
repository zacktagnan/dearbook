<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import Show from "@/Pages/Profile/Show.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NotificationBox from "@/Components/dearbook/NotificationBox.vue";
import { CameraIcon, XMarkIcon, CheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { Head } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
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
});

const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const imagesForm = useForm({
    cover: null,
    avatar: null,
})

const coverImageSrc = ref("");
const showNotification = ref(false)

const onCoverChange = (event) => {
    console.log(event);

    imagesForm.cover = event.target.files[0];
    if (imagesForm.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            console.log("OnLoad...");
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

    imagesForm.post(route('profile.update-images'), {
        onSuccess: () => {
            var fadeTarget = document.getElementById("notification-box");
            // fadeTarget.style.opacity = 1;
            // if (fadeTarget.style.opacity) {
            //     fadeTarget.style.removeProperty("opacity");
            //     // fadeInEffect()
            // }
            fadeTarget.style.removeProperty("opacity");

            showNotification.value = true
            resetCoverImage()

            // setTimeout(() => {
            //     // showNotification.value = false
            //     fadeOutEffect()
            // }, 3000)
        },
        onError: () => {
            showNotification.value = true
        },
        onFinish: () => {
            setTimeout(() => {
                // showNotification.value = false
                fadeOutEffect('notification')
            }, 3000)
        }
    })
}

/* Pasado al componente de NotificationBox...*/
const fadeOutEffect = (className) => {
    // var fadeTarget = document.getElementById("notification-box");
    // var fadeEffect = setInterval(function () {
    //     if (!fadeTarget.style.opacity) {
    //         fadeTarget.style.opacity = 1;
    //         // styleObject.opacity.value = 1
    //     }
    //     if (fadeTarget.style.opacity > 0) {
    //         fadeTarget.style.opacity -= 0.1;
    //     } else {
    //         clearInterval(fadeEffect);
    //     }
    //     // if (styleObject.opacity.value > 0) {
    //     //     styleObject.opacity.value -= 0.1;
    //     // } else {
    //     //     clearInterval(fadeEffect);
    //     // }
    // }, 50);
    var fadeTargetArr = document.getElementsByClassName(className);
    for (let i = 0; i < fadeTargetArr.length; i++) {
        var fadeTarget = fadeTargetArr[i];

        var fadeEffect = setInterval(function () {
            if (!fadeTarget.style.opacity) {
                fadeTarget.style.opacity = 1;
                // styleObject.opacity.value = 1
            }
            if (fadeTarget.style.opacity > 0) {
                fadeTarget.style.opacity -= 0.1;
            } else {
                clearInterval(fadeEffect);
            }
            // if (styleObject.opacity.value > 0) {
            //     styleObject.opacity.value -= 0.1;
            // } else {
            //     clearInterval(fadeEffect);
            // }
        }, 50);
    }
}
</script>

<template>

    <Head :title="$t('Profile')" />

    <AuthenticatedLayout>
        <!-- <div class="container pt-20 mx-auto "> -->
        <div class="bg-white">
            <div class="lg:w-2/3 mx-auto pt-[58px] relative">
                <!-- <div v-show="showNotification && status === 'cover-image-updated'"
                    class="absolute z-50 px-4 py-2 my-2 text-sm font-medium text-white rounded-md top-28 bg-emerald-600 dark:bg-emerald-400">
                    Cover image has been updated successfully!
                </div> -->
                <!-- <div class="z-50 toast" id="notification-box">
                    <div v-show="status === 'cover-image-updated'" class="shadow-lg alert">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            class="w-6 h-6 stroke-info shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Info</h3>
                            <div class="text-xs">Cover image has been updated successfully!</div>
                        </div>
                        <button @click="fadeOutEffect"
                            class="flex items-start p-1 font-bold text-white bg-red-200 rounded-full hover:bg-red-400"
                            title="Cerrar">
                            <XMarkIcon class="w-3 h-3" />
                        </button>
                    </div>
                </div> -->
                <NotificationBox @callFadeOutEffect="fadeOutEffect" v-show="showNotification" :title="'Info'"
                    :message="'Cover image has been updated successfully!'" />
                <NotificationBox @callFadeOutEffect="fadeOutEffect" v-if="showNotification && errors.cover"
                    :title="'Error'" :message="errors.cover" />

                <div class="relative bg-white">
                    <img :src="coverImageSrc || user.cover_url || '/img/default_cover.jpg'" alt="Cover"
                        class="object-cover object-top w-full h-[154px] md:h-[330px] md:rounded-es-lg md:rounded-ee-lg" />

                    <div class="absolute lg:right-5 lg:bottom-[172px] right-5 top-28 md:top-72 lg:top-auto">
                        <button v-if="!coverImageSrc"
                            class="flex items-center px-2 pt-[2px] pb-1 text-sm font-semibold text-gray-700 rounded bg-gray-50 hover:bg-gray-200"
                            title="Actualizar foto de portada">
                            <CameraIcon class="w-5 h-5 lg:mr-1" />

                            <span class="hidden lg:block mt-[2px]">Actualizar foto de portada</span>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="onCoverChange"
                                title="Actualizar foto de portada" />
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

                    <div class="flex flex-col items-center justify-between bg-white lg:flex-row">
                        <div class="flex flex-col w-full lg:block">
                            <div class="flex justify-center lg:justify-start lg:static">
                                <img :src="
                                        user.avatar_url ||
                                        '/img/default_avatar.png'
                                    " alt=""
                                    class="absolute top-20 md:top-64 lg:top-auto lg:left-11 lg:bottom-4 w-[168px] h-[168px] rounded-full border-4 border-white" />

                                <button
                                    class="absolute flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full right-[135px] md:right-[314px] lg:right-auto lg:left-[164px] top-[200px] md:top-[369px] lg:top-auto lg:bottom-7 hover:bg-gray-300"
                                    title="Actualizar foto de usuario">
                                    <CameraIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <div
                                class="lg:pl-[228px] pt-[16px] mb-4 lg:mb-0 flex flex-col items-center lg:items-start lg:mt-0 mt-[69px]">
                                <h1 class="text-[32px] font-extrabold">
                                    {{ user.name }}
                                </h1>
                                <small class="font-bold text-gray-600">69 seguidores</small>
                                <!-- <div
                                    class="lg:relative flex justify-center lg:justify-start w-full mt-1.5 lg:mb-6 gap-[1px] lg:gap-0">
                                    <button>
                                        <img src="/img/demo/avatar.png" alt=""
                                            class="lg:absolute z-30 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white" />
                                    </button>
                                    <button>
                                        <img src="/img/demo/avatar.png" alt=""
                                            class="lg:absolute z-20 lg:left-6 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white" />
                                    </button>
                                    <button>
                                        <img src="/img/demo/avatar.png" alt=""
                                            class="lg:absolute z-10 lg:left-12 w-[33px] h-[33px] rounded-full border-2 border-gray-400 lg:border-white" />
                                    </button>
                                </div> -->



                                <div class="relative mt-2.5 lg:mb-6">
                                    <div
                                        class="flex justify-center -space-x-1 font-mono text-sm font-bold leading-6 text-white lg:justify-start">
                                        <div class="z-40 flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                                            <a href="#">
                                                <img src="/img/demo/avatar.png" alt=""
                                                    class="w-[30px] h-[30px] rounded-full ring-2 ring-white dark:ring-slate-900" />
                                            </a>
                                        </div>
                                        <div
                                            class="z-30 flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            04</div>
                                        <div
                                            class="z-20 flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            03</div>
                                        <div
                                            class="z-10 flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            02</div>
                                        <div
                                            class="z-0 flex items-center justify-center w-[30px] h-[30px] bg-pink-500 rounded-full shadow-lg ring-2 ring-white dark:ring-slate-900">
                                            01</div>
                                    </div>
                                </div>




                            </div>
                        </div>

                        <div v-if="isMyProfile" class="flex items-end h-full mt-0 mb-4 lg:mt-16 lg:mb-0">
                            <PrimaryButton class="lg:mr-[47px] bg-cyan-600 hover:bg-cyan-500" title="Editar">
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
                    <div class="bg-white shadow">
                        <TabList class="flex mx-auto lg:px-8 lg:w-2/3">
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

                    <TabPanels class="px-4 mx-auto my-4 lg:px-0 lg:w-2/3">
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
