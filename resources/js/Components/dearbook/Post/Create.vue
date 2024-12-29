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

defineProps({
    group: {
        type: Object,
        default: null,
    },
})

const postToCreate = ref({
    id: null,
    body: '',
    user: usePage().props.auth.user,
})
const showCreateModal = ref(false)

const openCreateModal = () => {
    showCreateModal.value = true
}

// -------------------------------------

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const errorsFromPostToCreate = ref({})

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const activeShowNotification = (errors) => {
    errorsFromPostToCreate.value = errors
    // console.log('errorsFromPostToCreate', errorsFromPostToCreate)
    showNotification.value = true

    setTimeout(() => {
        closingNotification('notification')
    }, 3000)
}

const closingNotification = (className) => {
    if (notificationBoxRef.value) {
        notificationBoxRef.value.fadeOutEffect(className)
    }
}

const closeShowNotification = () => {
    showNotification.value = false
}
</script>

<template>
    <div class="flex gap-2 p-4 bg-white dark:bg-slate-800 border dark:border-slate-900 rounded">
        <a :href="route('profile.index', { username: $page.props.auth.user.username })"
            :title="'Perfil de ' + $page.props.auth.user.name">
            <img :src="$page.props.auth.user.avatar_url"
                class="w-10 transition-all border-2 rounded-full hover:border-cyan-500 bg-gray-50 dark:bg-gray-300"
                :alt="$page.props.auth.user.name" />
        </a>
        <div @click="openCreateModal"
            class="px-2.5 py-1.5 dark:py-2 text-gray-400 border-2 dark:border border-gray-200 dark:border-slate-500 rounded-md cursor-pointer w-full">
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

        <!-- {{
        'showNotification >> ' + showNotification
        + 'errorsFromPostToCreate >> ' + errorsFromPostToCreate
    + 'errorsFromPostToCreate.attachments >> ' + errorsFromPostToCreate.attachments
    + 'errorsFromPostToCreate.body >> ' + errorsFromPostToCreate.body
        }} -->

        <PostModal :post="postToCreate" :group="group" v-model="showCreateModal"
            @callActiveShowNotification="activeShowNotification" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-if="showNotification && errorsFromPostToCreate.attachments" :title="'Error'"
            :message="errorsFromPostToCreate.attachments" />
        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-else-if="showNotification && errorsFromPostToCreate.body" :title="'Error'"
            :message="errorsFromPostToCreate.body" />
        <!-- <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            v-else-if="showNotification && errorsFromPostToCreate && !errorsFromPostToCreate.attachments && !errorsFromPostToCreate.body"
            :title="'Error'" :message="'Errores en el formulario ... Revisar.'" /> -->
        <!-- <template v-if="showNotification && errorsFromPostToCreate">
            <NotificationBox v-for="errorFromPostToCreate of errorsFromPostToCreate" ref="notificationBoxRef"
                @callCloseShowNotification="closeShowNotification" :title="'Error'" :message="errorFromPostToCreate" />
        </template> -->
        <!-- <NotificationBox v-if="showNotification && errorsFromPostToCreate"
            v-for="errorFromPostToCreate of errorsFromPostToCreate" ref="notificationBoxRef"
            @callCloseShowNotification="closeShowNotification" :title="'Error'" :message="errorFromPostToCreate" /> -->
    </div>
</template>
