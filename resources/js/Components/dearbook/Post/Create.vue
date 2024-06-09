<script setup>
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import { usePage } from "@inertiajs/vue3";
import { ref } from 'vue';
// import TextareaInput from '@/Components/TextareaInput.vue';
// import { useForm } from '@inertiajs/vue3';

// const postCreating = ref(false)
// const closePostCreate = () => {
//     postCreating.value = false
//     // postCreateForm.body = ''
//     // o
//     postCreateForm.reset()
// }

// const postCreateForm = useForm({
//     body: ''
// })

// const submitPostCreate = () => {
//     postCreateForm.post(route('post.store'), {
//         onSuccess: () => {
//             closePostCreate()
//         },
//     })
// }

// ====================

const postToCreate = ref({
    id: null,
    body: '',
    user: usePage().props.auth.user,
})
const showCreateModal = ref(false)

const openCreateModal = () => {
    showCreateModal.value = true
}
</script>

<template>
    <div class="flex gap-2 p-4 bg-white border rounded">
        <a :href="route('profile.index', { username: $page.props.auth.user.username })"
            :title="'Perfil de ' + $page.props.auth.user.name">
            <img :src="$page.props.auth.user.avatar_url ||
            '/img/default_avatar.png'" class="w-[40px] rounded-full border-2 hover:border-cyan-500 transition-all"
                :alt="$page.props.auth.user.name" />
        </a>
        <div @click="openCreateModal"
            class="px-2.5 py-1.5 text-gray-500 border-2 border-gray-200 rounded-md cursor-pointer w-full">
            ¿Qué tienes en mente?
        </div>
        <!-- <TextareaInput v-else placeholder="Expresa lo que quieras comunicar"
            class="w-full" v-model="postCreateForm.body" autofocus></TextareaInput> -->

        <!-- <div v-if="postCreating" class="flex justify-between mt-3">
            <button
                @click="closePostCreate"
                class="px-3 py-2 text-sm font-semibold text-white bg-gray-800 rounded-md shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-800">
                Cancelar
            </button>

            <div>
                <button
                    class="relative px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Adjuntar archivos
                    <input type="file" class="absolute top-0 bottom-0 left-0 right-0 opacity-0 cursor-pointer" name=""
                        id="">
                </button>

                <button
                    @click="submitPostCreate" type="submit"
                    class="px-3 py-2 ml-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Enviar
                </button>
            </div>
        </div> -->

        <PostModal :post="postToCreate" v-model="showCreateModal" />
    </div>
</template>
