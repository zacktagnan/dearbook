<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import ArchiveMenuList from '@/Components/dearbook/ArchiveManagement/Menu/List.vue'
import ManageActivityLog from '@/Pages/ArchiveManagement/ActivityLog.vue'
import ManageArchive from '@/Pages/ArchiveManagement/Archive.vue'
import ManageTrash from '@/Pages/ArchiveManagement/Trash.vue'
import { onMounted, ref, shallowRef } from "vue";

const props = defineProps({
    success: {
        type: Object,
    },
});

const componentList = {
    'ManageActivityLog': ManageActivityLog,
    'ManageArchive': ManageArchive,
    'ManageTrash': ManageTrash,
}

const selectedComponent = shallowRef(null)
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
const componentRef = ref(null)

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
                componentRef.value.loadCurrentArchivedPosts(true)

                reInitDataAfterProcess(registerBoxToProcess.value.processType)
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

const reInitDataAfterProcess = (processType) => {
    if (processType === 'delete_all_selected') {
        selectedComponent.value = componentList['ManageArchive']
        archiveMenuListRef.value.setSelectedMenuItem('ManageArchive')
    }
    else if (processType === 'force_delete_all_selected') {
        selectedComponent.value = componentList['ManageTrash']
        archiveMenuListRef.value.setSelectedMenuItem('ManageTrash')
    }

    activeShowNotification()

    if (processType === 'delete_all_selected') {
        componentRef.value.loadCurrentArchivedPosts(true)
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
        selectedComponent.value = componentList['ManageTrash']
        archiveMenuListRef.value.setSelectedMenuItem('ManageTrash')
    }
    else if (props.success && props.success.from === 'archive') {
        selectedComponent.value = componentList['ManageArchive']
        archiveMenuListRef.value.setSelectedMenuItem('ManageArchive')
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
            <div class="fixed px-4 pt-20 pb-4 bg-white shadow-lg lg:min-h-full w-[315px] lg:col-span-2">
                <ArchiveMenuList ref="archiveMenuListRef" @callLoadComponent="loadComponent" />
            </div>

            <div class="ml-[315px] px-20 pt-4 pb-4 lg:pt-20 lg:col-span-10">
                <!-- Parte Central -->
                <template v-if="success">
                    <component :is="selectedComponent || ManageActivityLog" ref="componentRef"
                        @callConfirmProcess="showConfirmProcess" @callNotifyProcessEnding="notifyProcessEnding" />
                </template>
                <template v-else>
                    <KeepAlive>
                        <component :is="selectedComponent || ManageActivityLog" ref="componentRef"
                            @callConfirmProcess="showConfirmProcess" @callNotifyProcessEnding="notifyProcessEnding" />
                    </KeepAlive>
                </template>
            </div>

            <ConfirmPostForceDeletionModal :show="showingConfirmProcess" @close="closeConfirmProcess">
                <div class="p-6">
                    <template v-if="registerBoxToProcess.processType === 'delete'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.index.confirm_deletion.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.index.confirm_deletion.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'delete_all_selected'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.index.confirm_deletion_collection.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.index.confirm_deletion_collection.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'force_delete'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.index.confirm_force_deletion.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.index.confirm_force_deletion.message') }}
                        </p>
                    </template>
                    <template v-else-if="registerBoxToProcess.processType === 'force_delete_all_selected'">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $t('dearbook.post.index.confirm_force_deletion_collection.question') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $t('dearbook.post.index.confirm_force_deletion_collection.message') }}
                        </p>
                    </template>

                    <div class="flex justify-end mt-6">
                        <SecondaryButton @click="closeConfirmProcess" :title="$t('Cancel')"> {{ $t('Cancel') }}
                        </SecondaryButton>

                        <DangerButton v-if="registerBoxToProcess.processType === 'delete'" class="ms-3"
                            @click="deleting" :title="$t('dearbook.post.index.confirm_deletion.button_text')">
                            {{ $t('dearbook.post.index.confirm_deletion.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'delete_all_selected'"
                            class="ms-3" @click="deleting"
                            :title="$t('dearbook.post.index.confirm_deletion_collection.button_text')">
                            {{ $t('dearbook.post.index.confirm_deletion_collection.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'force_delete'" class="ms-3"
                            @click="forceDeleting"
                            :title="$t('dearbook.post.index.confirm_force_deletion.button_text')">
                            {{ $t('dearbook.post.index.confirm_force_deletion.button_text') }}
                        </DangerButton>
                        <DangerButton v-else-if="registerBoxToProcess.processType === 'force_delete_all_selected'"
                            class="ms-3" @click="forceDeleting"
                            :title="$t('dearbook.post.index.confirm_force_deletion_collection.button_text')">
                            {{ $t('dearbook.post.index.confirm_force_deletion_collection.button_text') }}
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
        </div>
    </AuthenticatedLayout>
</template>
