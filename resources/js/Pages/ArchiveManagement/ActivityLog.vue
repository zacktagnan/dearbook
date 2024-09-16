<script setup>
import ResponsiveDropDownMenu from '@/Components/dearbook/ArchiveManagement/DropDownMenu/Index.vue'
import NavBar from '@/Pages/ArchiveManagement/Partials/NavBar.vue'
import ListItem from '@/Pages/ArchiveManagement/Partials/Item.vue'
import { computed, ref, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

import axiosClient from '@/axiosClient'

const posts = ref({})

onMounted(() => {
    loadCurrentActivityLogPosts(false)
});

const allActivityLogPostIds = ref([])
const getAllActivityLogPostIds = () => {
    Object.keys(posts.value).forEach(index => {
        for (let item of posts.value[index]) {
            allActivityLogPostIds.value.push(item.id)
        }
    });
}

const postIdsForm = useForm({
    checked_ids: [],
    from: '',
})

const managementType = 'activity_log'

const navBarRef = ref(null)

const checkedAll = ref(false)
const checkedIds = ref([])

const checkAll = () => {
    checkedAll.value = !checkedAll.value
    checkedIds.value = []

    if (checkedAll.value) {
        checkedIds.value = allActivityLogPostIds.value
    }
}

const buttonDisabled = computed(
    () => checkedIds.value.length === 0
);

const emit = defineEmits(['callLoadComponent', 'callConfirmProcess', 'callNotifyProcessEnding'])

const submitProcess = (processType, postId) => {
    if (processType === 'archive') {
        processArchive(postId, managementType)
    } else if (processType === 'delete') {
        // processForceDelete(postId)
        emit('callConfirmProcess', 'delete', postId, managementType)
    }
}

const processArchive = (postId, from) => {
    axiosClient.get(route('post.archive', {
        id: postId,
        from,
    }))
        .then(() => {
            loadCurrentActivityLogPosts(true)
        })
        .catch((error) => {
            console.log('ERRORS-ACTIVITY_LOG', error.response.data.errors)
        })
}

const submitGlobalProcess = (processType) => {
    postIdsForm.checked_ids = checkedIds

    if (processType === 'archive_all_selected') {
        postIdsForm.from = managementType
        processGlobalArchive()
    } else if (processType === 'delete_all_selected') {
        // processGlobalForceDelete()
        emit('callConfirmProcess', 'delete_all_selected', checkedIds.value, managementType)
    }
}

const processGlobalArchive = () => {
    axiosClient.post(route('post.archive-all-selected'), postIdsForm)
        .then(() => {
            loadCurrentActivityLogPosts(true)
            emit('callNotifyProcessEnding', 'archive_all_selected_from_' + managementType)
        })
        .catch((error) => {
            // console.log('ERRORS-ARCHIVE_ALL', error.response.data.errors)
            console.log('ERRORS-ARCHIVE_ALL', error)
        })
}

const unMarkAll = () => {
    checkedAll.value = false
    checkedIds.value = []
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const reset = () => {
    allActivityLogPostIds.value = []
    unMarkAll()
}

const loadCurrentActivityLogPosts = async (hasToReset) => {
    try {
        const response = await axiosClient.get(route('archive-management.activity-log-posts'))
        posts.value = response.data.current_activity_log_posts

        if (hasToReset) {
            reset()
        }

        getAllActivityLogPostIds()
    } catch (error) {
        console.log('ERRORS-loadCurrentActivityLogPosts', error)
    }
}

const checkItem = () => {
    if (checkedIds.value.length === allActivityLogPostIds.value.length) {
        checkedAll.value = true
    } else {
        checkedAll.value = false
    }
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}

defineExpose({ loadCurrentActivityLogPosts, })
</script>

<template>
    <div>
        <ResponsiveDropDownMenu :management-type="managementType" @callLoadComponent="loadComponent" />

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

                <ListItem v-for="(post, index) of postsPerDay" :post="post" :index="index" :is-activity-log="true"
                    v-model="checkedIds" @callCheckItem="checkItem" @callSubmitProcess="submitProcess" />
            </div>
        </template>
    </div>
</template>
