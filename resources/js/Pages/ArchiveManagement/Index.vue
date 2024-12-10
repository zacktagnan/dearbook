<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import ArchiveMenuList from '@/Components/dearbook/ArchiveManagement/Menu/List.vue'
import ManagePostActivityLog from '@/Pages/ArchiveManagement/Post/ActivityLog.vue'
import ManagePostArchive from '@/Pages/ArchiveManagement/Post/Archive.vue'
import ManagePostTrash from '@/Pages/ArchiveManagement/Post/Trash.vue'
import ManageGroupTrash from '@/Pages/ArchiveManagement/Group/Trash.vue'
import { onMounted, ref, shallowRef } from "vue";

const props = defineProps({
    success: {
        type: Object,
    },
});

const componentList = {
    'ManagePostActivityLog': ManagePostActivityLog,
    'ManagePostArchive': ManagePostArchive,
    'ManagePostTrash': ManagePostTrash,
    // ------------------------------------------------
    'ManageGroupTrash': ManageGroupTrash,
}

const selectedComponent = shallowRef(null)
const componentRef = ref(null)
const archiveMenuListRef = ref(null)

const loadComponent = (index) => {
    selectedComponent.value = componentList[index]
    archiveMenuListRef.value.setSelectedMenuItem(index)
}

// ======================================================================

import ConfirmPostForceDeletionModal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const registerBoxToProcess = ref({})
const showingConfirmProcess = ref(false);

const showConfirmProcess = (processType, entityIdOrEntityIds, from) => {
    if (processType === 'delete') {
        registerBoxToProcess.value = {
            entityId: entityIdOrEntityIds,
            from,
            processType,
        }
    }
    else if (processType === 'delete_all_selected') {
        registerBoxToProcess.value = {
            entityIds: {
                checked_ids: entityIdOrEntityIds,
                from,
            },
            processType,
        }
    }
    else if (processType === 'force_delete') {
        registerBoxToProcess.value = {
            entityId: entityIdOrEntityIds,
            from,
            processType,
        }
    }
    else if (processType === 'force_delete_all_selected') {
        registerBoxToProcess.value = {
            entityIds: {
                checked_ids: entityIdOrEntityIds,
                from,
            },
            processType,
        }
    }

    showingConfirmProcess.value = true;
}

const closeConfirmProcess = () => {
    showingConfirmProcess.value = false;
}

const deleting = () => {
    if (registerBoxToProcess.value.processType === 'delete') {
        router.get(route('post.destroy-from-management', {
            id: registerBoxToProcess.value.entityId,
            from: registerBoxToProcess.value.from,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                closeConfirmProcess()
            },
        })
    }
    else if (registerBoxToProcess.value.processType === 'delete_all_selected') {
        router.post(route('post.destroy-from-management-all-selected'), registerBoxToProcess.value.entityIds, {
            onSuccess: () => {
                closeConfirmProcess()
                activeShowNotification()
                if (registerBoxToProcess.value.entityIds.from === 'activity_log') {
                    componentRef.value.loadCurrentActivityLogPosts(true)
                }
                if (registerBoxToProcess.value.entityIds.from === 'archive') {
                    componentRef.value.loadCurrentArchivedPosts(true)
                }

                reInitDataAfterProcess(registerBoxToProcess.value.processType, registerBoxToProcess.value.entityIds.from)
            },
        })
    }
}

const forceDeleting = () => {
    if (registerBoxToProcess.value.processType === 'force_delete') {
        router.get(route('post.force-destroy', {
            id: registerBoxToProcess.value.entityId,
            from: registerBoxToProcess.value.from,
        }), {
            preserveScroll: true,
            onSuccess: () => {
                closeConfirmProcess()
            },
        })
    }
    else if (registerBoxToProcess.value.processType === 'force_delete_all_selected') {
        router.post(route('post.force-destroy-all-selected'), registerBoxToProcess.value.entityIds, {
            onSuccess: () => {
                closeConfirmProcess()
                // Sin saber el por qué, a pesar de estar construido el proceso de igual forma tanto para el FORCE_DELETE individual, como para el de conjunto, en este de conjunto, son necesarias las siguientes instrucciones para actualizar el listado y resetear los elementos de Trash, así como para que se active el auto-cerrado de la notificación:
                //
                activeShowNotification()
                componentRef.value.loadCurrentTrashedPosts(true)

                reInitDataAfterProcess(registerBoxToProcess.value.processType)
            },
        })
    }
}

const reInitDataAfterProcess = (processType, from = '') => {
    if (processType === 'delete_all_selected') {
        if (from === 'activity_log') {
            selectedComponent.value = componentList['ManagePostActivityLog']
            archiveMenuListRef.value.setSelectedMenuItem('ManagePostActivityLog')
        }
        if (from === 'archive') {
            selectedComponent.value = componentList['ManagePostArchive']
            archiveMenuListRef.value.setSelectedMenuItem('ManagePostArchive')
        }
    }
    else if (processType === 'force_delete_all_selected') {
        selectedComponent.value = componentList['ManagePostTrash']
        archiveMenuListRef.value.setSelectedMenuItem('ManagePostTrash')
    }

    activeShowNotification()

    if (processType === 'delete_all_selected') {
        if (from === 'activity_log') {
            componentRef.value.loadCurrentActivityLogPosts(true)
        }
        if (from === 'archive') {
            componentRef.value.loadCurrentArchivedPosts(true)
        }
    }
    else if (processType === 'force_delete_all_selected') {
        componentRef.value.loadCurrentTrashedPosts(true)
    }
}

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const showNotification = ref(true)
const notificationBoxRef = ref(null)

const timer = ref(null)
const timeOnSeconds = ref(0)
const maxTimeOnSecondsForNotificationBox = usePage().props.maxTimeOnSecondsForNotificationBox;

const activeShowNotification = () => {
    showNotification.value = true

    // setTimeout(() => {
    //     closingNotification('notification')
    // }, 3000)
    startClosingNotification()
}

const startClosingNotification = () => {
    timer.value = setInterval(() => {
        // if (notificationBoxRef.value) {
        //     timeOnSeconds.value++
        //     console.log('timeOnSeconds', timeOnSeconds.value)

        //     if (timeOnSeconds.value >= maxTimeOnSecondsForNotificationBox) {
        //         stopClosingNotification()
        //         closingNotification('notification')
        //         console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        //     }
        // }
        if (notificationBoxRef.value && timeOnSeconds.value < maxTimeOnSecondsForNotificationBox) {
            ++timeOnSeconds.value
            // console.log('timeOnSeconds', timeOnSeconds.value)
        } else {
            if (timeOnSeconds.value >= maxTimeOnSecondsForNotificationBox) {
                stopClosingNotification()
                closingNotification('notification')
                // console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
            }
        }
    }, 1000)
}

const stopClosingNotification = () => {
    clearInterval(timer.value)
    props.success.from = null
    props.success.message = null
}

const closingNotification = (className) => {
    timer.value = null
    timeOnSeconds.value = 0
    // notificationBoxRef.value.fadeOutEffect(className)
    if (notificationBoxRef.value) {
        notificationBoxRef.value.fadeOutEffect(className)
    }
}

const closeShowNotification = () => {
    showNotification.value = false
    stopClosingNotification()
}

const notifyProcessEnding = (processType) => {
    console.log('notifyProcessEnding...')

    // props.success.from = 'trash'
    // props.success.message = 'Archivado en conjunto - OK'

    // activeShowNotification()
    // showNotification.value = true

    router.get(route('archive-management.notify.process.ending', processType), {
        preserveScroll: true,
        onSuccess: () => {
            // Parece ser que, al haber un "return->back()" tras terminar la petición demandada,
            // en vez de imprimirse esto, directamente, se pasa al onMounted de este archivo.
            console.log('OK - notifyProcessEnding...NOTIFICANDO.')
            // Por otro lado, al NotificationBox no se le llega a aplicar el efecto de FADEOUT al desaparecer.
        },
    })
}

onMounted(() => {
    // para procesos unitarios...
    if (props.success && props.success.from === 'trash') {
        selectedComponent.value = componentList['ManagePostTrash']
        archiveMenuListRef.value.setSelectedMenuItem('ManagePostTrash')
    }
    else if (props.success && props.success.from === 'archive') {
        selectedComponent.value = componentList['ManagePostArchive']
        archiveMenuListRef.value.setSelectedMenuItem('ManagePostArchive')
    }
    else if (props.success && props.success.from === 'activity_log') {
        selectedComponent.value = componentList['ManagePostActivityLog']
        archiveMenuListRef.value.setSelectedMenuItem('ManagePostActivityLog')
    }

    if (props.success) {
        console.log('HAY SUCCESS...:)')
        activeShowNotification()
        showNotification.value = true
    }
});
</script>

<template>

    <Head :title="$t('dearbook.archive_management.section_label')" />

    <AuthenticatedLayout>
        <div>
            <div
                class="hidden md:block md:fixed px-4 pt-5 pb-4 bg-white dark:bg-gray-800 dark:text-white shadow-lg lg:min-h-full w-[315px] lg:col-span-2">
                <ArchiveMenuList ref="archiveMenuListRef" @callLoadComponent="loadComponent" />
            </div>

            <div class="md:ml-[315px] px-4 md:px-20 pt-4 pb-4 lg:pt-5 lg:col-span-10">
                <!-- Parte Central -->
                <template v-if="success">
                    <component :is="selectedComponent || ManagePostActivityLog" ref="componentRef"
                        @callConfirmProcess="showConfirmProcess" @callNotifyProcessEnding="notifyProcessEnding"
                        @callLoadComponent="loadComponent" />
                </template>
                <template v-else>
                    <KeepAlive>
                        <component :is="selectedComponent || ManagePostActivityLog" ref="componentRef"
                            @callConfirmProcess="showConfirmProcess" @callNotifyProcessEnding="notifyProcessEnding"
                            @callLoadComponent="loadComponent" />
                    </KeepAlive>
                </template>
            </div>

            <ConfirmPostForceDeletionModal :show="showingConfirmProcess" @close="closeConfirmProcess">
                <div class="p-6">
                    <template v-if="registerBoxToProcess.processType === 'delete'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.confirm.deletion.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.confirm.deletion.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'delete_all_selected'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.confirm.deletion_collection.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.confirm.deletion_collection.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'force_delete'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.confirm.force_deletion.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.confirm.force_deletion.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'force_delete_all_selected'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.confirm.force_deletion_collection.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.confirm.force_deletion_collection.message') }}
                        </p>
                    </template>

                    <div class="flex justify-end mt-6">
                        <SecondaryButton @click="closeConfirmProcess" :title="$t('Cancel')"> {{ $t('Cancel') }}
                        </SecondaryButton>

                        <DangerButton v-if="registerBoxToProcess.processType === 'delete'" class="ms-3"
                            @click="deleting" :title="$t('dearbook.post.confirm.deletion.button_text')">
                            {{ $t('dearbook.post.confirm.deletion.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'delete_all_selected'"
                            class="ms-3" @click="deleting"
                            :title="$t('dearbook.post.confirm.deletion_collection.button_text')">
                            {{ $t('dearbook.post.confirm.deletion_collection.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'force_delete'" class="ms-3"
                            @click="forceDeleting"
                            :title="$t('dearbook.post.confirm.force_deletion.button_text')">
                            {{ $t('dearbook.post.confirm.force_deletion.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'force_delete_all_selected'"
                            class="ms-3" @click="forceDeleting"
                            :title="$t('dearbook.post.confirm.force_deletion_collection.button_text')">
                            {{ $t('dearbook.post.confirm.force_deletion_collection.button_text') }}
                        </DangerButton>
                    </div>
                </div>
            </ConfirmPostForceDeletionModal>

            <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                v-show="showNotification && success?.from === 'trash'" :title="'Info'" :message="success?.message" />
            <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                v-show="showNotification && success?.from === 'archive'" :title="'Info'" :message="success?.message" />
            <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
                @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
                v-show="showNotification && success?.from === 'activity_log'" :title="'Info'"
                :message="success?.message" />
        </div>
    </AuthenticatedLayout>
</template>
