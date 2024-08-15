<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ArchiveMenuList from '@/Components/dearbook/ArchiveManagement/Menu/List.vue'
import ManageActivityLog from '@/Pages/ArchiveManagement/ActivityLog.vue'
import ManageArchive from '@/Pages/ArchiveManagement/Archive.vue'
import ManageTrash from '@/Pages/ArchiveManagement/Trash.vue'
import { ref, shallowRef } from "vue";

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
                <KeepAlive>
                    <component :is="selectedComponent || ManageActivityLog" />
                </KeepAlive>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
