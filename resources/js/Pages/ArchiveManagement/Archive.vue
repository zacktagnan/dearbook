<script setup>
import NavBar from '@/Pages/ArchiveManagement/Partials/NavBar.vue'
import ListItem from '@/Pages/ArchiveManagement/Partials/Item.vue'
import { computed, ref, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

import axiosClient from '@/axiosClient'

const posts = ref({})

onMounted(() => {
    loadCurrentArchivedPosts(false)
});

const allArchivedPostIds = ref([])
const getAllArchivedPostIds = () => {
    Object.keys(posts.value).forEach(index => {
        for (let item of posts.value[index]) {
            allArchivedPostIds.value.push(item.id)
        }
    });
}

const postIdsForm = useForm({
    checked_ids: [],
    from: '',
})

const managementType = 'archive'

const navBarRef = ref(null)

const checkedAll = ref(false)
const checkedIds = ref([])

const checkAll = () => {
    checkedAll.value = !checkedAll.value
    checkedIds.value = []

    if (checkedAll.value) {
        checkedIds.value = allArchivedPostIds.value
    }
}

const buttonDisabled = computed(
    () => checkedIds.value.length === 0
);

const emit = defineEmits(['callConfirmProcess'])

const submitProcess = (processType, postId) => {
    if (processType === 'restore_from_archive') {
        processRestore(postId, managementType)
    } else if (processType === 'delete') {
        // processForceDelete(postId)
        emit('callConfirmProcess', 'delete', postId, managementType)
    }
}

const processRestore = (postId, from) => {
    axiosClient.get(route('post.restore', {
        id: postId,
        from,
    }))
        .then(() => {
            loadCurrentArchivedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-RESTORE', error.response.data.errors)
        })
}

const processForceDelete = (postId) => {
    axiosClient.get(route('post.force-destroy', postId))
        .then(() => {
            loadCurrentArchivedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-FORCE_DELETE', error.response.data.errors)
        })
}

const submitGlobalProcess = (processType) => {
    postIdsForm.checked_ids = checkedIds

    if (processType === 'restore_from_archive_all_selected') {
        postIdsForm.from = managementType
        processGlobalRestore()
    } else if (processType === 'delete_all_selected') {
        // processGlobalForceDelete()
        emit('callConfirmProcess', 'delete_all_selected', checkedIds.value, managementType)
    }
}

const processGlobalRestore = () => {
    axiosClient.post(route('post.restore-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentArchivedPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-RESTORE_ALL', error.response.data.errors)
        })
}

const processGlobalForceDelete = () => {
    axiosClient.post(route('post.force-destroy-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentArchivedPosts(true)
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
    allArchivedPostIds.value = []
    unMarkAll()
}

const loadCurrentArchivedPosts = async (hasToReset) => {
    try {
        const response = await axiosClient.get(route('post.archived-posts'))
        posts.value = response.data.current_archived_posts

        if (hasToReset) {
            reset()
        }

        getAllArchivedPostIds()
    } catch (error) {
        console.log('ERRORS-loadCurrentArchivedPosts', error)
    }
}

defineExpose({ loadCurrentArchivedPosts, })

const checkItem = () => {
    if (checkedIds.value.length === allArchivedPostIds.value.length) {
        checkedAll.value = true
    } else {
        checkedAll.value = false
    }
    navBarRef.value.checkAllRef.checked = checkedAll.value
}
</script>

<template>
    <div>
        <NavBar ref="navBarRef" :button-disabled="buttonDisabled" :checked-ids-length="checkedIds.length"
            :management-type="managementType" @callCheckAll="checkAll" @callUnMarkAll="unMarkAll"
            @callSubmitGlobalProcess="submitGlobalProcess" />

        <template v-if="posts.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(postsPerDay, day) of posts" class="px-3 py-4 mt-4 bg-white rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ postsPerDay.length }})</small></h4>

                <ListItem v-for="(post, index) of postsPerDay" :post="post" :index="index" v-model="checkedIds"
                    @callCheckItem="checkItem" @callSubmitProcess="submitProcess" />
            </div>
        </template>
    </div>
</template>
