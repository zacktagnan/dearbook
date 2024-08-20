<script setup>
import { ArchiveBoxIcon, ArrowUturnLeftIcon, TrashIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import TrashedItem from '@/Pages/ArchiveManagement/Partials/Item.vue'
import { computed, ref, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

import axiosClient from '@/axiosClient'

const posts = ref({})

onMounted(() => {
    loadCurrentTrashedPosts(false)
});

const allTrashedPostIds = ref([])
const getAllTrashedPostIds = () => {
    Object.keys(posts.value).forEach(index => {
        for (let item of posts.value[index]) {
            allTrashedPostIds.value.push(item.id)
        }
    });
}

const postIdsForm = useForm({
    checked_ids: [],
});

const checkAllTrashedRef = ref(null)
const checkedAll = ref(false)
const checkedIds = ref([])

const checkAll = () => {
    checkedAll.value = !checkedAll.value
    checkedIds.value = []

    if (checkedAll.value) {
        checkedIds.value = allTrashedPostIds.value
    }
}

const buttonDisabled = computed(
    () => checkedIds.value.length === 0
);

const emit = defineEmits(['callConfirmForceDeletion'])

const submitProcess = (processType, postId) => {
    if (processType === 'restore') {
        processRestore(postId)
    } else if (processType === 'force_delete') {
        // processForceDelete(postId)
        emit('callConfirmForceDeletion', 'force_delete', postId)
    }
}

const processRestore = (postId) => {
    axiosClient.get(route('post.restore', postId))
        .then(() => {
            loadCurrentTrashedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-RESTORE', error.response.data.errors)
        })
}

const processForceDelete = (postId) => {
    axiosClient.get(route('post.force-destroy', postId))
        .then(() => {
            loadCurrentTrashedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-FORCE_DELETE', error.response.data.errors)
        })
}

const submitGlobalProcess = (processType) => {
    postIdsForm.checked_ids = checkedIds

    if (processType === 'restore_all_selected') {
        processGlobalRestore()
    } else if (processType === 'force_delete_all_selected') {
        // processGlobalForceDelete()
        emit('callConfirmForceDeletion', 'force_delete_all_selected', checkedIds.value)
    }
}

const processGlobalRestore = () => {
    axiosClient.post(route('post.restore-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentTrashedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-RESTORE_ALL', error.response.data.errors)
        })
}

const processGlobalForceDelete = () => {
    axiosClient.post(route('post.force-destroy-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentTrashedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-FORCE_DELETE_ALL', error.response.data.errors)
        })
}

const unMarkAll = () => {
    checkedAll.value = false
    checkedIds.value = []
    checkAllTrashedRef.value.checked = checkedAll.value
}

const reset = () => {
    allTrashedPostIds.value = []
    unMarkAll()
}

const loadCurrentTrashedPosts = async (hasToReset) => {
    try {
        const response = await axiosClient.get(route('post.trashed-posts'))
        posts.value = response.data.current_trashed_posts

        if (hasToReset) {
            reset()
        }

        getAllTrashedPostIds()
    } catch (error) {
        console.log('ERRORS-loadCurrentTrashedPosts', error)
    }
}

defineExpose({ loadCurrentTrashedPosts, })

const checkItem = () => {
    if (checkedIds.value.length === allTrashedPostIds.value.length) {
        checkedAll.value = true
    } else {
        checkedAll.value = false
    }
    checkAllTrashedRef.value.checked = checkedAll.value
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between px-3 py-5 bg-white rounded-lg">
            <div class="flex items-center">
                <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-slate-200">
                    <input type="checkbox" ref="checkAllTrashedRef" @change="checkAll" name="check_all" id="check_all"
                        class="w-[22px] h-[22px] rounded group-hover/check_all:bg-slate-200">
                </div>
                <label for="check_all" class="ml-1 font-bold hover:cursor-pointer">Todo</label>
                <button v-if="!buttonDisabled" title="Desmarcar toda casilla marcada" @click="unMarkAll"
                    class="ml-5 flex items-center gap-1 pl-2 pr-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300">
                    <XMarkIcon class="w-5 h-5" />
                    Desmarcar
                </button>
            </div>

            <div v-if="checkedIds.length > 0">
                [ {{ checkedIds.length }} ]
            </div>

            <div class="flex items-center gap-5 pr-2">
                <button :disabled="buttonDisabled" :title="!buttonDisabled ? 'Archivar seleccionado(s)' : ''"
                    class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 disabled:bg-slate-100 disabled:text-gray-400">
                    <ArchiveBoxIcon class="w-5 h-5" />
                    Archivar
                </button>
                <button @click="submitGlobalProcess('restore_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Restaurar seleccionado(s)' : ''"
                    class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 disabled:bg-slate-100 disabled:text-gray-400">
                    <ArrowUturnLeftIcon class="w-5 h-5" />
                    Restaurar
                </button>
                <button @click="submitGlobalProcess('force_delete_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Eliminar seleccionado(s)' : ''"
                    class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 disabled:bg-slate-100 disabled:text-gray-400">
                    <TrashIcon class="w-5 h-5" />
                    Eliminar
                </button>
            </div>
        </div>

        <template v-if="posts.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(postsPerDay, day) of posts" class="px-3 py-4 mt-4 bg-white rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ postsPerDay.length }})</small></h4>

                <TrashedItem v-for="(post, index) of postsPerDay" :post="post" :index="index" v-model="checkedIds"
                    @callCheckItem="checkItem" @callSubmitProcess="submitProcess" />
            </div>
        </template>
    </div>
</template>
