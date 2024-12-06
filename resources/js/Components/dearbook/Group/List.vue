<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import GroupListTitle from '@/Components/dearbook/Group/ListTitle.vue'
import GroupListItems from '@/Components/dearbook/Group/ListItems.vue'
import GroupModal from '@/Components/dearbook/Group/Modal.vue'
import { usePage } from "@inertiajs/vue3";
import { ref } from 'vue';

const props = defineProps({
    groups: Array,
    searchGroupTerm: {
        type: String,
        default: '',
    },
})

const groupToCreate = ref({
    id: null,
    name: '',
    auto_approval: true,
    about: '',
    user: usePage().props.auth.user,
})

const showCreateGroupModal = ref(false)

const openCreateGroupModal = () => {
    showCreateGroupModal.value = true
}

const onGroupCreated = (group) => {
    props.groups.unshift(group)
}
</script>

<template>
    <div class="flex flex-col p-3 dark:text-white border dark:border-gray-800 rounded bg-sky-200 dark:bg-sky-900 sticky top-[4.8rem]">
        <div class="flex-1 block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full" :title="open ? 'Mostrar -' : 'Mostrar +'">
                    <div class="flex items-center justify-between">
                        <GroupListTitle />

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-6 h-6 transition-all" :class="open ? 'rotate-180 transform' : ''">
                            <path fill-rule="evenodd"
                                d="M11.47 7.72a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 1 1-1.06 1.06L12 9.31l-6.97 6.97a.75.75 0 0 1-1.06-1.06l7.5-7.5Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </DisclosureButton>

                <DisclosurePanel>
                    <GroupListItems :groups="groups" :search-group-term="searchGroupTerm"
                        @callOpenCreateGroupModal="openCreateGroupModal" />
                </DisclosurePanel>
            </Disclosure>
        </div>

        <div class="flex-col flex-1 hidden lg:block">
            <GroupListTitle />

            <GroupListItems :groups="groups" :search-group-term="searchGroupTerm" @callOpenCreateGroupModal="openCreateGroupModal" />
        </div>
    </div>

    <GroupModal :group="groupToCreate" v-model="showCreateGroupModal" @callGroupCreated="onGroupCreated" />
</template>
