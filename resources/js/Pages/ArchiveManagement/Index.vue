<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ArchiveMenuList from '@/Components/dearbook/ArchiveManagement/Menu/List.vue'
import ManageActivityLog from '@/Pages/ArchiveManagement/ActivityLog.vue'
import ManageArchive from '@/Pages/ArchiveManagement/Archive.vue'
import ManageTrash from '@/Pages/ArchiveManagement/Trash.vue'
import { ref, shallowRef } from "vue";

const props = defineProps({
    activityLogPosts: [String, Object],
    archivedPosts: [String, Object],
    trashedPosts: Object,
});

const componentList = {
    'ManageActivityLog': ManageActivityLog,
    'ManageArchive': ManageArchive,
    'ManageTrash': ManageTrash,
}

const selectedComponent = shallowRef(null)
const archiveMenuListRef = ref(null)
const postList = ref(props.activityLogPosts)

const loadComponent = (index) => {
    selectedComponent.value = componentList[index]
    archiveMenuListRef.value.setSelectedMenuItem(index)

    switch (index) {
        case 'ManageActivityLog':
            postList.value = props.activityLogPosts
            break;
        case 'ManageArchive':
            postList.value = props.archivedPosts
            break;
        case 'ManageTrash':
            postList.value = props.trashedPosts
            break;
        default:
            postList.value = props.activityLogPosts
            break;
    }
}
</script>

<template>

    <Head :title="$t('dearbook.archive_management.section_label')" />

    <AuthenticatedLayout>
        <!-- <div class="grid lg:grid-cols-12">
            <div class="px-4 pt-20 pb-4 bg-white shadow-lg lg:h-screen lg:col-span-2">
                <ArchiveMenuList ref="archiveMenuListRef" @callLoadComponent="loadComponent" />
            </div>

            <div class="px-4 pt-4 pb-4 overflow-auto lg:pt-20 lg:col-span-10 lg:h-screen">
                <component :is="selectedComponent || ManageActivityLog"></component>
            </div>
        </div> -->
        <!-- Con la estructura anterior, el ScrollToTop no llega a aparecer tras cierto desplazamiento vertical -->
        <div>
            <div class="fixed px-4 pt-20 pb-4 bg-white shadow-lg lg:min-h-full w-[315px] lg:col-span-2">
                <ArchiveMenuList ref="archiveMenuListRef" @callLoadComponent="loadComponent" />
            </div>

            <div class="ml-[315px] px-20 pt-4 pb-4 lg:pt-20 lg:col-span-10">
                <!-- Parte Central -->
                <component :is="selectedComponent || ManageActivityLog" :posts="postList"></component>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
