<script setup>
import { ref } from 'vue';

const postCreating = ref(false)
</script>

<template>
    <div class="p-4 bg-white border rounded">
        <div @click="postCreating=true" class="px-2 py-3 text-gray-500 border-2 border-gray-200 rounded">
            Pulsar aquí para una nueva publicación
        </div>

        <div v-if="postCreating" class="flex justify-between mt-3">
            <button type="submit"
                class="relative px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Adjuntar archivos
                <input type="file" class="absolute top-0 bottom-0 left-0 right-0 opacity-0 cursor-pointer" name=""
                    id="">
            </button>

            <button type="submit"
                class="px-3 py-2 ml-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Enviar
            </button>
        </div>
    </div>
</template>
