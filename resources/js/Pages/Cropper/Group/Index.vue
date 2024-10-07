<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, reactive } from "vue";

import { Cropper, Preview } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    group: {
        type: Object,
    },
});

const imagesForm = useForm({
    group_id: props.group.id,
    thumbnail: null,
})

const image = ref('')

const cropper = ref()
const file = ref()
const uploadImage = (event) => {
    let input = event.target.files[0];
    if (input) {
        const reader = new FileReader();
        reader.onload = () => {
            image.value = reader.result;
        };
        reader.readAsDataURL(input);
    }
}

// RECORTE y SUBIDA a servidor...
const saveImage = () => {
    const result = cropper.value.getResult();

    result.canvas.toBlob(blob => {
        var fadeTarget = document.getElementById("notification-box");
        fadeTarget.style.removeProperty("opacity");

        imagesForm.thumbnail = blob

        imagesForm.post(route('group.update-thumbnail-image'), {
            preserveScroll: true,
            onSuccess: () => {
                emit('callCloseCropImageModal')
                emit('callActiveShowNotification')
            },
            onError: () => {
                emit('callCloseCropImageModal')
                emit('callActiveShowNotification')
            },
            onFinish: () => {
                image.value = ''
            }
        })
    })
}

const sizePreview = reactive({
    width: null,
    height: null,
})

const minimumSize = {
    width: 168,
    height: 168,
}

const msgCheckSizePreview = ref('')
const checkSizePreview = () => {
    msgCheckSizePreview.value = (sizePreview.width < minimumSize.width || sizePreview.height < minimumSize.height)
        ? 'Dimensiones incorrectas'
        : 'Dimensiones correctas'
}

const resultPreview = reactive({
    image: null,
    coordinates: null,
})

const change = () => {
    const { image, coordinates } = cropper.value.getResult();

    sizePreview.width = coordinates.width
    sizePreview.height = coordinates.height

    resultPreview.image = image
    resultPreview.coordinates = coordinates
    checkSizePreview()
}

const flip = (x, y) => {
    const { image } = cropper.value.getResult();
    if (image.transforms.rotate % 180 !== 0) {
        cropper.value.flip(!x, !y);
    } else {
        cropper.value.flip(x, y);
    }
}

const rotate = (angle) => {
    cropper.value.rotate(angle)
}

const emit = defineEmits([
    'callCloseCropImageModal',
    'callActiveShowNotification',
])
</script>

<template>

    <Head :title="$t('Cropping')" />

    <div class="w-[90%] mx-auto">
        <div class="mt-4 space-y-6">
            <div
                class="flex items-center justify-between px-4 shadow bg-slate-400 md:px-4 sm:py-4 dark:bg-gray-800 sm:rounded-lg">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Imagen de Perfil de Grupo
                </h2>

                <div class="flex flex-col items-start w-20 py-1.5 sm:py-0 text-xs leading-none sm:leading-4 text-gray-500 cursor-pointer"
                    :title="msgCheckSizePreview">
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
            </div>
        </div>

        <div class="p-4 mt-4 bg-black rounded-ss-md rounded-se-md">
            <Cropper ref="cropper" class="h-[400px] mx-auto max-h-[500px] w-full"
                :src="image || group.thumbnail_url || '/img/default_thumbnail_group.png'" :stencil-props="{
                    aspectRatio: 1 / 1
                }" :auto-zoom="true" @change="change" :debounce="false" />
        </div>

        <div
            class="flex items-center justify-between w-full px-4 py-3 my-4 sm:items-stretch sm:px-11 bg-slate-400 rounded-es-md rounded-ee-md">
            <div class="border border-white rounded-full max-h-[118px] sm:max-h-max">
                <Preview title="Preview" class="m-2 bg-white rounded-full" :width="100" :height="100"
                    :coordinates="resultPreview.coordinates" :image="resultPreview.image" />
            </div>

            <div
                class="w-7/12 flex flex-col-reverse sm:flex-row justify-between gap-2 sm:gap-0.5 p-2 border border-white rounded-md">
                <div class="flex justify-around sm:px-2 gap-x-2 sm:gap-x-0 sm:flex-col">
                    <button title="Upload"
                        class="flex justify-center items-center gap-1.5 px-2 py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                        @click="$refs.file.click()">
                        <input class="hidden" type="file" ref="file" @change="uploadImage($event)" accept="image/*" />
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

                <div class="sm:pr-1.5 flex flex-col gap-2 sm:gap-0 justify-around py-0">
                    <div class="flex justify-between gap-3">
                        <div class="text-right grow">Flip:</div>

                        <div class="flex">
                            <div class="flex gap-2">
                                <button title="Flip horizontal"
                                    class="flex justify-center items-center w-full gap-1.5 px-[5px] py-1 text-gray-400 transition-colors duration-200 delay-80 bg-gray-700 border border-gray-700 rounded-md cursor-pointer hover:text-white hover:bg-gray-800 hover:border-white group"
                                    @click="flip(true, false)">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-[22px] h-[22px] rotate-180">
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

                    <div class="flex justify-between gap-3">
                        <div class="text-right grow">Rotate:</div>

                        <div class="flex">
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
    </div>
</template>
