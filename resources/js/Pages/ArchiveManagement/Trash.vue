<script setup>
import { ArchiveBoxIcon, ArrowUturnLeftIcon, TrashIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import TrashedItem from '@/Pages/ArchiveManagement/Partials/Item.vue'
import { ref, onMounted } from 'vue';
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

const submitProcess = (processType, postId) => {
    if (processType === 'restore') {
        processRestore(postId)
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

const submitGlobalProcess = (processType) => {
    postIdsForm.checked_ids = checkedIds

    if (processType === 'restore') {
        processGlobalRestore()
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
};

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

// ====================================================================================================================
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const authUser = usePage().props.auth.user;

const showOptions = ref(true)
const isPostAuthor = computed(
    (post) => {
        showOptions.value = authUser && authUser.id === post.user.id
    }
    // () => {
    //     return (post) => authUser && authUser.id === post.user.id
    // }
);

const isTrashed = computed(
    // (post) => post.deleted_at !== ''
    () => {
        return (post) => post.deleted_at !== ''
    }
);

const loadImage = (post) => {
    let image = ''
    if (post.attachments.length > 0) {
        image = post.attachments[0].path
    } else {
        image = post.user.avatar_path
    }

    if (image) {
        return 'storage/' + image
    }
    return null
}

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
                <button @click="submitGlobalProcess('restore')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Restaurar seleccionado(s)' : ''"
                    class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 disabled:bg-slate-100 disabled:text-gray-400">
                    <ArrowUturnLeftIcon class="w-5 h-5" />
                    Restaurar
                </button>
                <button :disabled="buttonDisabled" :title="!buttonDisabled ? 'Eliminar seleccionado(s)' : ''"
                    class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 disabled:bg-slate-100 disabled:text-gray-400">
                    <TrashIcon class="w-5 h-5" />
                    Eliminar
                </button>
            </div>
        </div>

        <!-- <span>allTrashedPostIds: {{ allTrashedPostIds }}</span>
        <br>
        <span>checkedIds: {{ checkedIds }}</span>
        <pre>{{ posts }}</pre> -->

        <template v-if="posts.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(postsPerDay, day) of posts" class="px-3 py-4 mt-4 bg-white rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ postsPerDay.length }})</small></h4>

                <!-- <TrashedItem v-for="(post, index) of postsPerDay" :post="post" :index="index" /> -->
                <div v-for="(post, index) of postsPerDay" class="flex items-center justify-between py-2 gap-7"
                    :class="index > 0 ? 'border-t border-slate-300' : ''">
                    <div class="flex items-center">
                        <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-black/5">
                            <input type="checkbox" :value="post.id" v-model="checkedIds" @change="checkItem"
                                class="w-[22px] h-[22px] rounded group-hover/check_all:bg-black/5">
                        </div>
                    </div>

                    <div class="w-full p-2 rounded-lg hover:bg-black/5">
                        <!-- <pre>{{ loadImage(post) }}</pre> -->
                        <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                            class="flex items-center w-full gap-2 p-1">
                        <img :src="loadImage(post) || '/img/default_avatar.png'"
                            class="w-[60px] h-[60px] border-2 rounded-full" />
                        <div class="text-sm text-left">
                            <div v-html="post.body || '(sin texto)'"></div>
                            <small class="lowercase">{{ post.created_at_time }}</small>
                        </div>
                        <!-- <span>{{ post.id }}</span> -->
                        </Link>
                    </div>

                    <div class="pr-2">
                        <OptionsDropDown v-model="showOptions" :is-trashed="isTrashed(post)" @callRestoreItem="submitProcess('restore', post.id)"
                            @callForceDeleteItem="''" :ellipsis-type-icon="'vertical'" :item-type="'post'" />
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
