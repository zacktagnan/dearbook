<script setup>
import ResponsiveDropDownMenu from '@/Components/dearbook/ArchiveManagement/DropDownMenu/Index.vue'
import NavBar from '@/Pages/ArchiveManagement/Partials/Post/NavBar.vue'
import PostListItem from '@/Pages/ArchiveManagement/Partials/Post/Item.vue'
import { computed, ref, onMounted } from 'vue';
import { router, useForm } from "@inertiajs/vue3";

import axiosClient from '@/axiosClient'

const posts = ref({})

onMounted(() => {
    loadCurrentTrashedPosts(false)
});

const allTrashedPostIds = ref([])
const getAllTrashedPostIds = () => {
    Object.keys(posts.value).forEach(index => {
        for (let item of posts.value[index]) {
            // allTrashedPostIds.value.push(item.id)
            // Solo los eliminados por el autor del Post...
            if (item.user_id === item.deleted_by) {
                allTrashedPostIds.value.push(item.id)
            }
        }
    });
}

const postIdsForm = useForm({
    checked_ids: [],
    from: '',
})

const managementSection = 'posts'
const managementType = 'trash'

const navBarRef = ref(null)

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
)

const emit = defineEmits(['callLoadComponent', 'callConfirmProcess', 'callNotifyProcessEnding'])

const submitProcess = (processType, postId) => {
    if (processType === 'archive') {
        processArchive(postId, managementType)
    } else if (processType === 'restore_from_trash') {
        processRestore(postId, managementType)
    } else if (processType === 'force_delete') {
        // processForceDelete(postId)
        emit('callConfirmProcess', 'force_delete', postId, managementType)
    }
}

const processArchive = (postId, from) => {
    // Con un único parámetro para la ruta de GET...
    // axiosClient.get(route('post.restore', postId))
    // Con varios parámetros para la ruta de GET...
    axiosClient.get(route('post.archive', {
        id: postId,
        // Cuando el nombre del parámetro de ruta y el nombre de la variable que da el valor es el mismo...
        from,
    }))
        .then(() => {
            loadCurrentTrashedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-ARCHIVE', error.response.data.errors)
        })
}

const processRestore = (postId, from) => {
    axiosClient.get(route('post.restore', {
        id: postId,
        from,
    }))
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

    if (processType === 'archive_all_selected') {
        postIdsForm.from = managementType
        processGlobalArchive()
    } else if (processType === 'restore_from_trash_all_selected') {
        postIdsForm.from = managementType
        processGlobalRestore()
    } else if (processType === 'force_delete_all_selected') {
        // processGlobalForceDelete()
        emit('callConfirmProcess', 'force_delete_all_selected', checkedIds.value, managementType)
    }
}

const processGlobalArchive = () => {
    axiosClient.post(route('post.archive-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentTrashedPosts(true)
            emit('callNotifyProcessEnding', 'archive_all_selected_from_' + managementType)
        })
        .catch((error) => {
            // console.log('ERRORS-ARCHIVE_ALL', error.response.data.errors)
            console.log('ERRORS-ARCHIVE_ALL', error)
        })
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
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const reset = () => {
    allTrashedPostIds.value = []
    unMarkAll()
}

const loadCurrentTrashedPosts = async (hasToReset) => {
    try {
        const response = await axiosClient.get(route('archive-management.trashed-posts'))
        posts.value = response.data.current_trashed_posts || []

        if (hasToReset) {
            reset()
        }

        getAllTrashedPostIds()
    } catch (error) {
        console.log('ERRORS-loadCurrentTrashedPosts', error)
        posts.value = [];
    }
}

const checkItem = () => {
    if (checkedIds.value.length === allTrashedPostIds.value.length) {
        checkedAll.value = true
    } else {
        checkedAll.value = false
    }
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}

defineExpose({ loadCurrentTrashedPosts, })
</script>

<template>
    <div>
        <ResponsiveDropDownMenu :management-section="managementSection" :management-type="managementType"
            @callLoadComponent="loadComponent" />

        <NavBar ref="navBarRef" :button-disabled="buttonDisabled" :checked-ids-length="checkedIds.length"
            :management-type="managementType" @callCheckAll="checkAll" @callUnMarkAll="unMarkAll"
            @callSubmitGlobalProcess="submitGlobalProcess" />

        <template v-if="posts.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white dark:bg-gray-800 dark:text-gray-100 rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(postsPerDay, day) of posts" class="px-3 py-4 mt-4 bg-white dark:bg-gray-800 dark:text-gray-100 rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ postsPerDay.length }})</small></h4>

                <PostListItem v-for="(post, index) of postsPerDay" :post="post" :index="index" v-model="checkedIds"
                    @callCheckItem="checkItem" @callSubmitProcess="submitProcess" />
            </div>
        </template>
    </div>
</template>
