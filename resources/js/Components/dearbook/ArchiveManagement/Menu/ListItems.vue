<script setup>
import ArchiveMenuListItem from '@/Components/dearbook/ArchiveManagement/Menu/Item.vue';
import TextInput from '@/Components/TextInput.vue';
import { ListBulletIcon, ArchiveBoxIcon, TrashIcon, } from "@heroicons/vue/24/outline";
import { ref } from 'vue';

const searchKeyword = ref('')

const menuItems = {
    'posts': [
        {
            title: 'activity_log',
            withMarginTop: false,
            icon: ListBulletIcon,
            componentName: 'ManagePostActivityLog',
        },
        {
            title: 'archive',
            withMarginTop: true,
            icon: ArchiveBoxIcon,
            componentName: 'ManagePostArchive',
        },
        {
            title: 'trash',
            withMarginTop: true,
            icon: TrashIcon,
            componentName: 'ManagePostTrash',
        },
    ],
    'groups': [
        {
            title: 'trash',
            withMarginTop: true,
            icon: TrashIcon,
            componentName: 'ManageGroupTrash',
        },
    ],
}

const emit = defineEmits(['callLoadComponent'])

const loadComponent = (componentName) => {
    emit('callLoadComponent', componentName)
}

const selectedMenuItem = ref('ManagePostActivityLog')
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
        <template v-for="(menuItem, index) of menuItems" :key="index">
            <h3 class="text-lg font-bold" :class="{ 'mt-3': index === 'groups' }">
                {{ $t('dearbook.archive_management.menu.' + index + '.header') }}
            </h3>
            <div>
                <ArchiveMenuListItem v-for="item of menuItem"
                    :title="$t('dearbook.archive_management.menu.' + index + '.items.' + item.title)"
                    :component-name="item.componentName" :is-selected="item.componentName === selectedMenuItem"
                    :class="{ 'mt-1': item.withMarginTop }" @callLoadComponent="loadComponent">
                    <template #icon>
                        <!-- <ListBulletIcon class="w-5 h-5" /> -->
                        <component :is="item.icon" class="w-5 h-5" />
                    </template>
                </ArchiveMenuListItem>
            </div>
        </template>
    </div>
</template>
