<script setup>
import { ListBulletIcon, ArchiveBoxIcon, TrashIcon, } from "@heroicons/vue/24/outline"
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import DropDownMenuItem from '@/Components/dearbook/ArchiveManagement/DropDownMenu/Item.vue'
import { ref } from 'vue'

defineProps({
    managementSection: String,
    managementType: String,
});

const showingNavigationDropdown = ref(false);

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

const emit = defineEmits(['callLoadComponent']);

const loadComponent = (componentName, showingNavigationDropdownValue) => {
    showingNavigationDropdown.value = showingNavigationDropdownValue
    emit('callLoadComponent', componentName)
}
</script>

<template>
    <div class="md:hidden w-full bg-white rounded-lg mb-4">
        <!-- Management Navigation Menu -->
        <div class="px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between h-14">
                <div class="flex">
                    <h3 class="text-xl font-bold flex gap-1 items-center">
                        {{ $t('dearbook.archive_management.menu.posts.items.' + managementType) }}
                        <span class="text-sm italic font-normal">
                            ({{ $t('dearbook.archive_management.menu.' + managementSection + '.header') }})
                        </span>
                    </h3>
                </div>

                <!-- Hamburger -->
                <div class="flex items-center -me-2 sm:hidden pr-3">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center px-1.5 py-1 text-sky-400 transition duration-150 ease-in-out rounded-md dark:text-sky-500 hover:text-sky-500 dark:hover:text-sky-400 bg-sky-50 hover:bg-sky-100 dark:hover:bg-sky-900 focus:outline-none focus:bg-sky-100 dark:focus:bg-sky-900 focus:text-sky-500 dark:focus:text-sky-400">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{
                                hidden: showingNavigationDropdown,
                                'inline-flex': !showingNavigationDropdown,
                            }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{
                                hidden: !showingNavigationDropdown,
                                'inline-flex': showingNavigationDropdown,
                            }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
            <!-- Responsive Settings Options -->
            <div class="pt-1 pb-2 border-t border-gray-200 dark:border-gray-600">
                <template v-for="(menuItem, index) of menuItems" :key="index">
                    <h4 class="ml-5 mt-1.5 text-sm font-bold">
                        {{ $t('dearbook.archive_management.menu.' + index + '.header') }}
                    </h4>
                    <div class="mt-0">
                        <DropDownMenuItem v-for="item of menuItem"
                            :title="$t('dearbook.archive_management.menu.' + index + '.items.' + item.title)"
                            :component-name="item.componentName" :class="{ 'mt-1': item.withMarginTop }"
                            @callLoadComponent="loadComponent">
                            <template #icon>
                                <component :is="item.icon" class="w-4 h-4" />
                            </template>
                        </DropDownMenuItem>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
