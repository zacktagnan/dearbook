<script setup>
import ArchiveMenuListItem from '@/Components/dearbook/ArchiveManagement/Menu/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ListBulletIcon, ArchiveBoxIcon, TrashIcon, } from "@heroicons/vue/24/outline";
import { ref } from 'vue';

const searchKeyword = ref('')

const menuItems = [
    {
        title: 'activity_log',
        withMarginTop: false,
        icon: ListBulletIcon,
        componentName: 'ManageActivityLog',
    },
    {
        title: 'archive',
        withMarginTop: true,
        icon: ArchiveBoxIcon,
        componentName: 'ManageArchive',
    },
    {
        title: 'trash',
        withMarginTop: true,
        icon: TrashIcon,
        componentName: 'ManageTrash',
    },
]

const emit = defineEmits(['callLoadComponent'])

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}

const selectedMenuItem = ref('ManageActivityLog')
const setSelectedMenuItem = (name) => {
    selectedMenuItem.value = name
}

defineExpose({ setSelectedMenuItem, })
</script>

<template>
    <TextInput class="w-full mt-2" :model-value="searchKeyword"
        :placeholder="$t('dearbook.archive_management.search.placeholder')" />

    <hr class="mt-4 border-white">

    <div class="py-2 mt-1 lg:flex-1">
        <div>
            <ArchiveMenuListItem v-for="item of menuItems"
                :title="$t('dearbook.archive_management.menu.items.' + item.title)" :component-name="item.componentName"
                :is-selected="item.componentName === selectedMenuItem" :class="{ 'mt-1': item.withMarginTop }"
                @callLoadComponent="loadComponent">
                <template #icon>
                    <!-- <ListBulletIcon class="w-5 h-5" /> -->
                    <component :is="item.icon" class="w-5 h-5" />
                </template>
            </ArchiveMenuListItem>
        </div>
    </div>
</template>
