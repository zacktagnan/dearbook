<script setup>
import ResponsiveDropDownMenu from '@/Components/dearbook/ArchiveManagement/DropDownMenu/Index.vue'
import NavBar from '@/Pages/ArchiveManagement/Partials/Group/NavBar.vue'
import GroupListItem from '@/Pages/ArchiveManagement/Partials/Group/Item.vue'
import { computed, ref, onMounted } from 'vue';
import { useForm } from "@inertiajs/vue3";

import axiosClient from '@/axiosClient'

const groups = ref({})

onMounted(() => {
    loadCurrentTrashedGroups(false)
});

const allTrashedGroupIds = ref([])
const getAllTrashedGroupIds = () => {
    Object.keys(groups.value).forEach(index => {
        for (let item of groups.value[index]) {
            allTrashedGroupIds.value.push(item.id)
        }
    });
}

const groupIdsForm = useForm({
    checked_ids: [],
    from: '',
})

const managementSection = 'groups'
const managementType = 'trash'

const navBarRef = ref(null)

const checkedAll = ref(false)
const checkedIds = ref([])

const checkAll = () => {
    checkedAll.value = !checkedAll.value
    checkedIds.value = []

    if (checkedAll.value) {
        checkedIds.value = allTrashedGroupIds.value
    }
}

const buttonDisabled = computed(
    () => checkedIds.value.length === 0
)

const emit = defineEmits(['callLoadComponent', 'callConfirmProcess', 'callNotifyProcessEnding'])

const submitProcess = (processType, groupId) => {
    if (processType === 'archive') {
        processArchive(groupId, managementType)
    } else if (processType === 'restore_from_trash') {
        processRestore(groupId, managementType)
    } else if (processType === 'force_delete') {
        // processForceDelete(groupId)
        emit('callConfirmProcess', 'force_delete', groupId, managementType)
    }
}

const processArchive = (groupId, from) => {
    // Con un único parámetro para la ruta de GET...
    // axiosClient.get(route('group.restore', groupId))
    // Con varios parámetros para la ruta de GET...
    axiosClient.get(route('group.archive', {
        id: groupId,
        // Cuando el nombre del parámetro de ruta y el nombre de la variable que da el valor es el mismo...
        from,
    }))
        .then(() => {
            loadCurrentTrashedGroups(true)
        })
        .catch((error) => {
            console.log('ERRORS-GROUP-ARCHIVE', error.response.data.errors)
        })
}

const processRestore = (groupId, from) => {
    axiosClient.get(route('group.restore', {
        id: groupId,
        from,
    }))
        .then(() => {
            loadCurrentTrashedGroups(true)
        })
        .catch((error) => {
            console.log('ERRORS-GROUP-RESTORE', error.response.data.errors)
        })
}

const processForceDelete = (groupId) => {
    axiosClient.get(route('group.force-destroy', groupId))
        .then(() => {
            loadCurrentTrashedGroups(true)
        })
        .catch((error) => {
            console.log('ERRORS-GROUP-FORCE_DELETE', error.response.data.errors)
        })
}

const submitGlobalProcess = (processType) => {
    groupIdsForm.checked_ids = checkedIds

    if (processType === 'archive_all_selected') {
        groupIdsForm.from = managementType
        processGlobalArchive()
    } else if (processType === 'restore_from_trash_all_selected') {
        groupIdsForm.from = managementType
        processGlobalRestore()
    } else if (processType === 'force_delete_all_selected') {
        // processGlobalForceDelete()
        emit('callConfirmProcess', 'force_delete_all_selected', checkedIds.value, managementType)
    }
}

const processGlobalArchive = () => {
    axiosClient.post(route('group.archive-all-selected'), groupIdsForm)
        .then(() => {
            loadCurrentTrashedGroups(true)
            emit('callNotifyProcessEnding', 'archive_all_selected_from_' + managementType)
        })
        .catch((error) => {
            // console.log('ERRORS-ARCHIVE_ALL', error.response.data.errors)
            console.log('ERRORS-GROUP-ARCHIVE_ALL', error)
        })
}

const processGlobalRestore = () => {
    axiosClient.post(route('group.restore-all-selected'), groupIdsForm)
        .then(() => {
            loadCurrentTrashedGroups(true)
        })
        .catch((error) => {
            console.log('ERRORS-GROUP-RESTORE_ALL', error.response.data.errors)
        })
}

const processGlobalForceDelete = () => {
    axiosClient.post(route('group.force-destroy-all-selected'), groupIdsForm)
        .then(() => {
            loadCurrentTrashedGroups(true)
        })
        .catch((error) => {
            console.log('ERRORS-GROUP-FORCE_DELETE_ALL', error.response.data.errors)
        })
}

const unMarkAll = () => {
    checkedAll.value = false
    checkedIds.value = []
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const reset = () => {
    allTrashedGroupIds.value = []
    unMarkAll()
}

const loadCurrentTrashedGroups = async (hasToReset) => {
    try {
        const response = await axiosClient.get(route('archive-management.trashed-groups'))
        groups.value = response.data.current_trashed_groups

        if (hasToReset) {
            reset()
        }

        getAllTrashedGroupIds()
    } catch (error) {
        console.log('ERRORS-loadCurrentTrashedGroups', error)
    }
}

const checkItem = () => {
    if (checkedIds.value.length === allTrashedGroupIds.value.length) {
        checkedAll.value = true
    } else {
        checkedAll.value = false
    }
    navBarRef.value.checkAllRef.checked = checkedAll.value
}

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}

defineExpose({ loadCurrentTrashedGroups, })
</script>

<template>
    <div>
        <ResponsiveDropDownMenu :management-section="managementSection" :management-type="managementType"
            @callLoadComponent="loadComponent" />

        <NavBar ref="navBarRef" :button-disabled="buttonDisabled" :checked-ids-length="checkedIds.length"
            :management-type="managementType" @callCheckAll="checkAll" @callUnMarkAll="unMarkAll"
            @callSubmitGlobalProcess="submitGlobalProcess" />

        <template v-if="groups.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(groupsPerDay, day) of groups" class="px-3 py-4 mt-4 bg-white rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ groupsPerDay.length }})</small></h4>

                <GroupListItem v-for="(group, index) of groupsPerDay" :group="group" :index="index" v-model="checkedIds"
                    @callCheckItem="checkItem" @callSubmitProcess="submitProcess" />
            </div>
        </template>
    </div>
</template>
