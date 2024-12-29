<script setup>
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";
import { ShieldExclamationIcon } from "@heroicons/vue/24/outline";
import ChiefAdminStarIcon from '@/Components/Icons/Star.vue'
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from "vue";

const props = defineProps({
    group: Object,
});

const authUser = usePage().props.auth.user;

const authUserIsTheOwnerGroup = computed(() => authUser && authUser.id === props.group.user.id)
</script>

<template>
    <div class="rounded-md cursor-pointer hover:bg-white dark:hover:bg-[#102c38]">
        <Link :href="route('group.profile', { group: group.slug })" class="flex items-start gap-2 px-2 py-1">
        <img :src="group.thumbnail_url" class="mt-1 w-[36px] rounded-md border-[1px] border-gray-300 dark:border-gray-400 bg-gray-50 dark:bg-gray-400" :alt="group.name">
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black">{{ group.name }}</h3>

                <div class="flex gap-1">
                    <template v-if="group.role === 'admin'">
                        <span v-if="authUserIsTheOwnerGroup" :title="$t('dearbook.group.list.main.title.owner')">
                            <ChiefAdminStarIcon class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5" fill-content="#fff" />
                        </span>
                        <span v-else :title="$t('dearbook.group.list.main.title.role.admin')">
                            <AdjustmentsHorizontalIcon
                                class="w-[21px] h-[21px] text-cyan-500 rounded-full bg-white p-[0.5px] border-[0.5px] border-gray-200 dark:border-gray-400" />
                        </span>
                    </template>

                    <span v-if="group.status === 'pending'" :title="$t('dearbook.group.list.main.title.status.pending')">
                        <ShieldExclamationIcon
                            class="w-[21px] h-[21px] text-orange-500 rounded-full bg-white p-[0.5px]" />
                    </span>
                </div>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-300">{{ group.short_description ||
                $t('dearbook.group.list.main.no_description') }}
            </div>
        </div>
        </Link>
    </div>
</template>
