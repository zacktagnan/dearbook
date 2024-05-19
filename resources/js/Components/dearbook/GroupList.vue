<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import GroupListTitle from '@/Components/dearbook/GroupListTitle.vue'
import GroupListItems from '@/Components/dearbook/GroupListItems.vue'
</script>

<template>
    <div class="flex flex-col p-3 bg-white border rounded">
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
                    <GroupListItems />
                </DisclosurePanel>
            </Disclosure>
        </div>

        <div class="flex-col flex-1 hidden lg:block">
            <GroupListTitle />

            <GroupListItems />
        </div>
    </div>
</template>
