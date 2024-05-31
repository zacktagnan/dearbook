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
});

const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const imagesForm = useForm({
    cover: null,
    avatar: null,
})

const coverImageSrc = ref("");
const avatarImageSrc = ref("");
const showNotification = ref(true)

const activeShowNotification = () => {
    showNotification.value = true

    setTimeout(() => {
        fadeOutEffect('notification')
    }, 3000)
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

const onAvatarChange = (event) => {
    console.log(event);

    imagesForm.avatar = event.target.files[0];
    if (imagesForm.avatar) {
        const reader = new FileReader();
        reader.onload = () => {
            console.log("OnLoad AVATAR ...");
            avatarImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.avatar);
    }
};

const resetCoverImage = () => {
    coverImageSrc.value = ''
    imagesForm.cover = null
}

const resetAvatarImage = () => {
    avatarImageSrc.value = ''
    imagesForm.avatar = null
}

const submitCoverImage = () => {
    console.log(imagesForm.cover)

    var fadeTarget = document.getElementById("notification-box");
    fadeTarget.style.removeProperty("opacity");
    // showNotification.value = true

    imagesForm.post(route('profile.update-cover-image'), {
        onSuccess: () => {
            // fadeTarget.style.opacity = 1;
            // if (fadeTarget.style.opacity) {
            //     fadeTarget.style.removeProperty("opacity");
            //     // fadeInEffect()
            // }

            // fadeTarget.style.removeProperty("opacity");
            showNotification.value = true
            resetCoverImage()

            // setTimeout(() => {
            //     // showNotification.value = false
            //     fadeOutEffect()
            // }, 3000)
        },
        onError: () => {
            // console.log('INI - Hubo errores...', props.errors.cover, showNotification.value)
            // fadeTarget.style.removeProperty("opacity");
            showNotification.value = true
            // console.log('FIN - Hubo errores...', props.errors.cover, showNotification.value)
        },
        onFinish: () => {
            setTimeout(() => {
                // showNotification.value = false
                fadeOutEffect('notification')
            }, 3000)
        }
    })
}

const submitAvatarImage = () => {
    console.log(imagesForm.avatar)

    var fadeTarget = document.getElementById("notification-box");
    fadeTarget.style.removeProperty("opacity");

    imagesForm.post(route('profile.update-avatar-image'), {
        onSuccess: () => {
            showNotification.value = true
            resetAvatarImage()
        },
        onError: () => {
            showNotification.value = true
        },
        onFinish: () => {
            setTimeout(() => {
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

    setTimeout(() => {
        showNotification.value = false
    }, 1000)
}


// -----------------------------------------------

import Modal from '@/Components/Modal.vue';
import CropperIndex from '@/Pages/Cropper/Index.vue'

const showingCropImageModal = ref(false);
const showCropImageModal = () => {
    showingCropImageModal.value = true;
};
const closeCropImageModal = () => {
    showingCropImageModal.value = false;
};

// import { Cropper, Preview } from 'vue-advanced-cropper'
// import 'vue-advanced-cropper/dist/style.css'
// import { reactive } from "vue";

// const image = ref('')

// const cropper = ref()
// const file = ref()
// const uploadImage = (event) => {
//     let input = event.target.files[0];
//     if (input) {
//         const reader = new FileReader();
//         reader.onload = () => {
//             console.log("OnLoad AVATAR ...");
//             image.value = reader.result;
//         };
//         reader.readAsDataURL(input);
//     }
// }

// // CROP hacia otra pestaña...
// // const cropImage = () => {
// //     //     if (cropper.value) {
// //     const result = cropper.value.getResult();
// //     const newTab = window.open();
// //     newTab.document.body.innerHTML = `<img src="${result.canvas.toDataURL(
// //         "image/jpeg"
// //     )}"></img>`;

// //     closeCropImageModal()
// // }

// // RECORTE y SUBIDA a servidor...
// const saveImage = () => {
//     //     if (cropper.value) {
//     const result = cropper.value.getResult();

//     result.canvas.toBlob(blob => {
//         var fadeTarget = document.getElementById("notification-box");
//         fadeTarget.style.removeProperty("opacity");

//         imagesForm.avatar = blob

//         imagesForm.post(route('profile.update-avatar-image'), {
//             onSuccess: () => {
//                 closeCropImageModal()
//                 showNotification.value = true
//             },
//             onError: () => {
//                 closeCropImageModal()
//                 showNotification.value = true
//             },
//             onFinish: () => {
//                 setTimeout(() => {
//                     fadeOutEffect('notification')
//                 }, 3000)
//             }
//         })
//     })

//     // const newTab = window.open();
//     // newTab.document.body.innerHTML = `<img src="${result.canvas.toDataURL(
//     //     "image/jpeg"
//     // )}"></img>`;

//     // closeCropImageModal()
// }

// const sizePreview = reactive({
//     width: null,
//     height: null,
// })

// const minimumSize = {
//     width: 168,
//     height: 168,
// }

// const resultPreview = reactive({
//     // image: {
//     //     src: null
//     // },
//     // coordinates: {
//     //     width: null,
//     //     height: null,
//     // },
//     image: null,
//     coordinates: null,
// })
// // const change = () => {
// //     console.log('getRESULT', cropper.value.getResult())
// //     imagePreview.src = cropper.value.getResult().src;
// // }
// const change = () => {
//     console.log('getRESULT', cropper.value.getResult())
//     const { image, coordinates } = cropper.value.getResult();
//     console.log('COORD.', coordinates)
//     // resultPreview.coordinates.width = coordinates.width
//     // resultPreview.coordinates.height = coordinates.height
//     sizePreview.width = coordinates.width
//     sizePreview.height = coordinates.height
//     console.log('IMAGE', image)
//     resultPreview.image = image
//     resultPreview.coordinates = coordinates
// }

// const flip = (x, y) => {
//     const { image } = cropper.value.getResult();
//     if (image.transforms.rotate % 180 !== 0) {
//         // this.$refs.cropper.flip(!x, !y);
//         cropper.value.flip(!x, !y);
//     } else {
//         // this.$refs.cropper.flip(x, y);
//         cropper.value.flip(x, y);
//     }
// }

// const rotate = (angle) => {
//     cropper.value.rotate(angle)
// }

// -----------------------------------------------
</script>

<template>

    <Head :title="$t('Profile')" />

    <AuthenticatedLayout>
        <!-- <div class="container pt-20 mx-auto "> -->
        <div class="bg-white">
            <div class="lg:w-2/3 mx-auto pt-[58px] relative">
                <!-- <div v-show="showNotification && success === 'cover-image-updated'"
                    class="absolute z-50 px-4 py-2 my-2 text-sm font-medium text-white rounded-md top-28 bg-emerald-600 dark:bg-emerald-400">
                    Cover image has been updated successfully!
                </div> -->
                <!-- <div class="z-50 toast" id="notification-box">
                    <div v-show="success === 'cover-image-updated'" class="shadow-lg alert">
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
                <NotificationBox @callFadeOutEffect="fadeOutEffect" v-show="showNotification && success" :title="'Info'"
                    :message="success" />
                <NotificationBox @callFadeOutEffect="fadeOutEffect" v-if="showNotification && errors.cover"
                    :title="'Error'" :message="errors.cover" />
                <NotificationBox @callFadeOutEffect="fadeOutEffect" v-if="showNotification && errors.avatar"
                    :title="'Error'" :message="errors.avatar" />

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
                                <div
                                    class="absolute p-1 bg-white rounded-full top-20 md:top-64 lg:top-auto lg:left-7 lg:bottom-4">
                                    <img :src="
                                            avatarImageSrc || user.avatar_url ||
                                            '/img/default_avatar.png'
                                        " alt=""
                                        class="rounded-full border-[1px] border-gray-200 w-[174px] h-[168px]" />
                                </div>

                                <div
                                    class="absolute right-[135px] md:right-[314px] lg:right-auto lg:left-[164px] top-[200px] md:top-[369px] lg:top-auto lg:bottom-7">
                                    <button v-if="!avatarImageSrc" @click="showCropImageModal"
                                        class="flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300"
                                        title="Actualizar foto de perfil">
                                        <CameraIcon class="w-5 h-5" />
                                    </button>

                                    <!-- <button v-if="!avatarImageSrc"
                                        class="flex items-center p-[6px] text-sm font-semibold text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300"
                                        title="Actualizar foto de perfil">
                                        <CameraIcon class="w-5 h-5" />

                                        <input type="file" class="absolute inset-0 opacity-0 cursor-pointer"
                                            @change="onAvatarChange" title="Actualizar foto de portada" />
                                    </button> -->

                                    <div v-else class="flex flex-col gap-2">
                                        <button @click="resetAvatarImage"
                                            class="flex items-center p-1 text-sm font-semibold text-gray-100 rounded-full bg-red-400/65 hover:bg-red-600/65"
                                            title="Cancelar">
                                            <XMarkIcon class="w-5 h-5" />
                                        </button>
                                        <button @click="submitAvatarImage"
                                            class="flex items-center p-1 text-sm font-semibold text-gray-100 rounded-full bg-emerald-400/65 hover:bg-emerald-600/65"
                                            title="Enviar">
                                            <CheckIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="lg:pl-[228px] pt-[16px] mb-4 lg:mb-0 flex flex-col items-center lg:items-start lg:mt-0 mt-20">
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

        <Modal :show="showingCropImageModal" @close="closeCropImageModal">
            <CropperIndex @callFadeOutEffect="fadeOutEffect" @callCloseCropImageModal="closeCropImageModal"
            @callActiveShowNotification="activeShowNotification"
            :user="user" />

            <!-- Sin estilos Tailwind CSS -->
            <!-- <div>
                <div class="upload-example">
                    <Cropper ref="cropper" class="upload-example-cropper" :src="image" :auto-zoom="true" />
                    <Navigation :zoom="zoom" @change="onZoom" />
                    <div class="my-4 button-wrapper">
                        <span class="button" @click="$refs.file.click()">
                            <input type="file" ref="file" @change="uploadImage($event)" accept="image/*" />
                            Upload image
                        </span>
                        <span class="button" @click="saveImage">Crop image</span>
                    </div>
                </div>
            </div> -->

            <!-- Con  estilos Tailwind CSS -->
            <!-- <Navigation :zoom="zoom" @change="onZoom" /> -->

            <!-- <Preview
                        :width="120"
                        :height="120"

                        :image="result.image"
                        :coordinates="result.coordinates"

                        :image="imagePreview.src"
                    /> -->
            <!-- <div class="w-[90%] mx-auto">
                <div class="relative p-4 mt-4 bg-black rounded-ss-md rounded-se-md">
                    <Cropper ref="cropper" class="h-[400px] mx-auto max-h-[500px] bg-gray-500 w-full"
                        :src="image || user.avatar_url || '/img/default_avatar.png'" :stencil-props="{
                            aspectRatio: 1/1
                        }" :auto-zoom="true" @change="change" :debounce="false" />
                </div>

                <div class="absolute flex flex-col items-start w-20 text-xs text-gray-500 top-5 right-10">
                    <div class="flex justify-between w-full" :class="{
                            'text-red-700': sizePreview.width < minimumSize.width
                        }">
                        <div>width:</div>
                        <div>{{ sizePreview.width }}px</div>
                    </div>
                    <div class="flex justify-between w-full" :class="{
                            'text-red-700': sizePreview.height < minimumSize.height
                        }">
                        <div>height:</div>
                        <div>{{ sizePreview.height }}px</div>
                    </div>
                </div>

                <div class="flex justify-between w-full py-3 my-4 px-11 bg-slate-400 rounded-es-md rounded-ee-md">
                    <div class="border border-white rounded-full ">
                        <Preview title="Preview" class="m-2 rounded-full" :width="100" :height="100"
                            :coordinates="resultPreview.coordinates" :image="resultPreview.image" />
                    </div>

                    <div class="w-7/12 flex justify-between gap-0.5 p-2 border border-white rounded-md">
                        <div class="flex flex-col justify-around px-2">
                            <button title="Upload"
                                class="flex justify-center items-center gap-1.5 px-2 py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                @click="$refs.file.click()">
                                <input class="hidden" type="file" ref="file" @change="uploadImage($event)"
                                    accept="image/*" />
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 transition-colors duration-200 delay-80 group-hover:fill-white fill-gray-400">
                                    <path
                                        d="M12.5535 2.49392C12.4114 2.33852 12.2106 2.25 12 2.25C11.7894 2.25 11.5886 2.33852 11.4465 2.49392L7.44648 6.86892C7.16698 7.17462 7.18822 7.64902 7.49392 7.92852C7.79963 8.20802 8.27402 8.18678 8.55352 7.88108L11.25 4.9318V16C11.25 16.4142 11.5858 16.75 12 16.75C12.4142 16.75 12.75 16.4142 12.75 16V4.9318L15.4465 7.88108C15.726 8.18678 16.2004 8.20802 16.5061 7.92852C16.8118 7.64902 16.833 7.17462 16.5535 6.86892L12.5535 2.49392Z">
                                    </path>
                                    <path
                                        d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z">
                                    </path>
                                </svg>
                                Upload
                            </button>

                            <button title="Save"
                                class="flex justify-center items-center w-full gap-1.5 px-2 py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer group hover:text-white hover:bg-gray-800 hover:border-white"
                                @click="saveImage">
                                <svg version="1.1"
                                    class="w-4 h-4 transition-colors duration-200 delay-80 group-hover:fill-white fill-gray-400"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 32 32" xml:space="preserve">
                                    <path class="feather_een"
                                        d="M27,2H5C3.343,2,2,3.343,2,5v22c0,1.657,1.343,3,3,3h22c1.657,0,3-1.343,3-3V5C30,3.343,28.657,2,27,2z M25,3v9H7V3H25z M10,29v-8h12v8H10z M29,27c0,1.103-0.897,2-2,2h-4v-8c0-0.552-0.448-1-1-1H10c-0.552,0-1,0.448-1,1v8H5 c-1.103,0-2-0.897-2-2V5c0-1.103,0.897-2,2-2h1v9c0,0.552,0.448,1,1,1h18c0.552,0,1-0.448,1-1V3h1c1.103,0,2,0.897,2,2V27z M20,11h3 c0.552,0,1-0.448,1-1V5c0-0.552-0.448-1-1-1h-3c-0.552,0-1,0.448-1,1v5C19,10.552,19.448,11,20,11z M20,5h3v5h-3V5z M20,23.5 c0,0.276-0.224,0.5-0.5,0.5h-7c-0.276,0-0.5-0.224-0.5-0.5c0-0.276,0.224-0.5,0.5-0.5h7C19.776,23,20,23.224,20,23.5z M20,25.5 c0,0.276-0.224,0.5-0.5,0.5h-7c-0.276,0-0.5-0.224-0.5-0.5c0-0.276,0.224-0.5,0.5-0.5h7C19.776,25,20,25.224,20,25.5z">
                                    </path>
                                </svg>
                                Save
                            </button>
                        </div>

                        <div class="pr-1.5 flex flex-col justify-around py-0">
                            <div class="flex gap-3">
                                <div class="text-right grow">Flip:</div>

                                <div class="flex items-end">
                                    <div class="flex gap-2">
                                        <button title="Flip horizontal"
                                            class="flex justify-center items-center w-full gap-1.5 px-[5px] py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                            @click="flip(true, false)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                class="w-[22px] h-[22px]">
                                                <path
                                                    class="transition-colors duration-200 delay-80 group-hover:stroke-white stroke-gray-400"
                                                    d="M11 3L11 21" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path
                                                    class="transition-colors duration-200 delay-80 group-hover:fill-white fill-gray-400"
                                                    fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20 8.2L20 8.16146C20 7.63431 20 7.17955 19.9694 6.80497C19.9371 6.40963 19.8658 6.01641 19.673 5.63803C19.3854 5.07354 18.9265 4.6146 18.362 4.32698C17.9836 4.13419 17.5904 4.06287 17.195 4.03057C16.8205 3.99997 16.3657 3.99998 15.8385 4L15.8 4L14 4L14 6L15.8 6C16.3766 6 16.7488 6.00078 17.0322 6.02393C17.3038 6.04612 17.4045 6.0838 17.454 6.10899C17.6422 6.20487 17.7951 6.35785 17.891 6.54601C17.9162 6.59545 17.9539 6.69617 17.9761 6.96784C17.9992 7.25117 18 7.62345 18 8.2L18 15.8C18 16.3766 17.9992 16.7488 17.9761 17.0322C17.9539 17.3038 17.9162 17.4045 17.891 17.454C17.7951 17.6422 17.6422 17.7951 17.454 17.891C17.4045 17.9162 17.3038 17.9539 17.0322 17.9761C16.7488 17.9992 16.3766 18 15.8 18L14 18L14 20L15.8 20L15.8385 20C16.3657 20 16.8204 20 17.195 19.9694C17.5904 19.9371 17.9836 19.8658 18.362 19.673C18.9265 19.3854 19.3854 18.9265 19.673 18.362C19.8658 17.9836 19.9371 17.5904 19.9694 17.195C20 16.8205 20 16.3657 20 15.8385L20 15.8L20 8.2ZM12 20L12 18L8.2 18C7.62344 18 7.25117 17.9992 6.96783 17.9761C6.69617 17.9539 6.59545 17.9162 6.54601 17.891C6.35785 17.7951 6.20486 17.6422 6.10899 17.454C6.0838 17.4045 6.04612 17.3038 6.02393 17.0322C6.00078 16.7488 6 16.3766 6 15.8L6 8.2C6 7.62345 6.00078 7.25117 6.02393 6.96784C6.04612 6.69617 6.0838 6.59545 6.10899 6.54601C6.20487 6.35785 6.35785 6.20487 6.54601 6.10899C6.59545 6.0838 6.69617 6.04612 6.96783 6.02393C7.25117 6.00078 7.62345 6 8.2 6L12 6L12 4L8.2 4L8.16146 4C7.63431 3.99998 7.17954 3.99997 6.80497 4.03057C6.40963 4.06287 6.01641 4.13419 5.63803 4.32698C5.07354 4.6146 4.6146 5.07354 4.32698 5.63803C4.13419 6.01641 4.06287 6.40963 4.03057 6.80497C3.99997 7.17955 3.99998 7.63432 4 8.16148L4 8.2L4 15.8L4 15.8385C3.99998 16.3657 3.99997 16.8205 4.03057 17.195C4.06287 17.5904 4.13419 17.9836 4.32698 18.362C4.6146 18.9265 5.07354 19.3854 5.63803 19.673C6.01641 19.8658 6.40963 19.9371 6.80497 19.9694C7.17955 20 7.63432 20 8.16148 20L8.2 20L12 20Z">
                                                </path>
                                            </svg>
                                        </button>

                                        <button title="Flip vertical"
                                            class="flex justify-center items-center w-full gap-1.5 px-[5px] py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                            @click="flip(false, true)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                class="w-[22px] h-[22px]">
                                                <path
                                                    class="transition-colors duration-200 delay-80 group-hover:stroke-white stroke-gray-400"
                                                    d="M3 13H21" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path
                                                    class="transition-colors duration-200 delay-80 group-hover:fill-white fill-gray-400"
                                                    fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M8.2 4H8.16146C7.63431 3.99998 7.17955 3.99997 6.80497 4.03057C6.40963 4.06287 6.01641 4.13419 5.63803 4.32698C5.07354 4.6146 4.6146 5.07354 4.32698 5.63803C4.13419 6.01641 4.06287 6.40963 4.03057 6.80497C3.99997 7.17954 3.99998 7.63431 4 8.16146V8.2V10H6V8.2C6 7.62345 6.00078 7.25117 6.02393 6.96784C6.04612 6.69617 6.0838 6.59545 6.10899 6.54601C6.20487 6.35785 6.35785 6.20487 6.54601 6.10899C6.59545 6.0838 6.69617 6.04612 6.96784 6.02393C7.25117 6.00078 7.62345 6 8.2 6H15.8C16.3766 6 16.7488 6.00078 17.0322 6.02393C17.3038 6.04612 17.4045 6.0838 17.454 6.10899C17.6422 6.20487 17.7951 6.35785 17.891 6.54601C17.9162 6.59545 17.9539 6.69617 17.9761 6.96784C17.9992 7.25117 18 7.62345 18 8.2V10H20V8.2V8.16148C20 7.63432 20 7.17955 19.9694 6.80497C19.9371 6.40963 19.8658 6.01641 19.673 5.63803C19.3854 5.07354 18.9265 4.6146 18.362 4.32698C17.9836 4.13419 17.5904 4.06287 17.195 4.03057C16.8205 3.99997 16.3657 3.99998 15.8385 4H15.8H8.2ZM20 12H18V15.8C18 16.3766 17.9992 16.7488 17.9761 17.0322C17.9539 17.3038 17.9162 17.4045 17.891 17.454C17.7951 17.6422 17.6422 17.7951 17.454 17.891C17.4045 17.9162 17.3038 17.9539 17.0322 17.9761C16.7488 17.9992 16.3766 18 15.8 18H8.2C7.62345 18 7.25117 17.9992 6.96784 17.9761C6.69617 17.9539 6.59545 17.9162 6.54601 17.891C6.35785 17.7951 6.20487 17.6422 6.10899 17.454C6.0838 17.4045 6.04612 17.3038 6.02393 17.0322C6.00078 16.7488 6 16.3766 6 15.8V12H4V15.8V15.8385C3.99998 16.3657 3.99997 16.8205 4.03057 17.195C4.06287 17.5904 4.13419 17.9836 4.32698 18.362C4.6146 18.9265 5.07354 19.3854 5.63803 19.673C6.01641 19.8658 6.40963 19.9371 6.80497 19.9694C7.17955 20 7.63432 20 8.16148 20H8.2H15.8H15.8385C16.3657 20 16.8205 20 17.195 19.9694C17.5904 19.9371 17.9836 19.8658 18.362 19.673C18.9265 19.3854 19.3854 18.9265 19.673 18.362C19.8658 17.9836 19.9371 17.5904 19.9694 17.195C20 16.8205 20 16.3657 20 15.8385V15.8V12Z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="text-right grow">Rotate:</div>

                                <div class="flex items-end">
                                    <div class="flex gap-2">
                                        <button title="Rotate to left"
                                            class="flex justify-center items-center w-full gap-1.5 p-1 px-1.5 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                            @click="rotate(-90)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5 transition-colors duration-200 delay-80 group-hover:stroke-white stroke-gray-400">
                                                <path
                                                    d="M3.51018 14.9907C4.15862 16.831 5.38765 18.4108 7.01208 19.492C8.63652 20.5732 10.5684 21.0972 12.5165 20.9851C14.4647 20.873 16.3237 20.1308 17.8133 18.8704C19.303 17.61 20.3426 15.8996 20.7756 13.997C21.2086 12.0944 21.0115 10.1026 20.214 8.32177C19.4165 6.54091 18.0617 5.06746 16.3539 4.12343C14.6461 3.17941 12.6777 2.81593 10.7454 3.08779C7.48292 3.54676 5.32746 5.91142 3 8M3 8V2M3 8H9"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                            </svg>
                                        </button>

                                        <button title="Rotate to right"
                                            class="flex justify-center items-center w-full gap-1.5 p-1 px-1.5 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                            @click="rotate(90)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5 transition-colors duration-200 delay-80 group-hover:stroke-white stroke-gray-400">
                                                <path
                                                    d="M20.4898 14.9907C19.8414 16.831 18.6124 18.4108 16.9879 19.492C15.3635 20.5732 13.4316 21.0972 11.4835 20.9851C9.5353 20.873 7.67634 20.1308 6.18668 18.8704C4.69703 17.61 3.65738 15.8996 3.22438 13.997C2.79138 12.0944 2.98849 10.1026 3.78602 8.32177C4.58354 6.54091 5.93827 5.06746 7.64608 4.12343C9.35389 3.17941 11.3223 2.81593 13.2546 3.08779C16.5171 3.54676 18.6725 5.91142 21 8M21 8V2M21 8H15"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </Modal>
    </AuthenticatedLayout>
</template>
