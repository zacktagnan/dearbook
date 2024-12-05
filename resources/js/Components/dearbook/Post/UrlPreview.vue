<script setup>
import {
    XMarkIcon,
} from "@heroicons/vue/24/solid"
import { computed } from "vue";

const props = defineProps({
    isTherePreviewData: Boolean,
    url: String,
    preview: [Object, String],
    classes: String,
    imgClasses: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['callRemovePreview'])

const previewImageUrl = computed(() => {
    const imageUrl = props.preview.image

    if (!isValidImage(imageUrl)) {
        return null
    }

    // Verifica si es una URL absoluta válida (https:// o http://)
    if (isAbsoluteUrl(imageUrl)) {
        return imageUrl
    }

    // Si es una URL relativa que empieza con '/', la concatenamos con la URL base
    if (imageUrl && imageUrl.startsWith('/')) {
        return cleanUrl(props.url) + imageUrl
    }

    // Si no es relativa, asumimos que es una URL relativa sin '/' al principio
    else if (imageUrl) {
        return cleanUrl(props.url) + '/' + imageUrl
    }

    // Si no es una URL válida, retornamos null, lo que hace que la etiqueta <img> no se renderice
    return null
})

// Función para verificar si una URL es absoluta (empieza con 'http://' o 'https://')
const isAbsoluteUrl = (url) => {
    return /^(https?:\/\/)/i.test(url)
}

// Función para limpiar la URL base y evitar barras dobles
const cleanUrl = (url) => {
    // Elimina cualquier barra final en la URL base
    return url.replace(/\/$/, '')
}

const isValidImage = (url) => {
  // Aquí podrías validar si es una URL de imagen (por ejemplo, verificando la extensión)
  return /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(url)
}
</script>

<template>
    <div v-if="isTherePreviewData" :class="classes">
        <button @click="$emit('callRemovePreview')" title="Eliminar previo"
            class="absolute flex items-center justify-center text-red-100 transition-all bg-red-300 rounded-full opacity-0 cursor-pointer size-7 group-hover:opacity-100 hover:bg-red-400 right-3 top-3">
            <XMarkIcon class="size-5" />
        </button>
        <a :href="url" target="_blank" title="Acceder">
            <img v-if="previewImageUrl" :src="previewImageUrl" class="max-w-full p-2 rounded-ss-2xl rounded-se-2xl" :class="imgClasses" :alt="preview.title" />
            <div class="p-2">
                <h3 v-if="preview.title" class="font-semibold">{{ preview.title }}</h3>
                <p v-if="preview.description" class="text-sm">{{ preview.description }}</p>
            </div>
        </a>
    </div>
    <div v-else-if="url" :class="classes">
        <a :href="url" target="_blank" title="Acceder">
            <div class="p-2">
                <p>{{ url }}</p>
            </div>
        </a>
    </div>
</template>
