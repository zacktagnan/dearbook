<script setup>
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";
import { ShieldExclamationIcon } from "@heroicons/vue/24/outline";
import { Link } from '@inertiajs/vue3';

defineProps({
    group: Object,
});
</script>

<template>
    <div class="rounded-md cursor-pointer hover:bg-white">
        <Link :href="route('group.profile', { group: group.slug })" class="flex items-start gap-2 px-2 py-1">
        <img :src="group.thumbnail_url" class="mt-1 w-[36px] rounded-full" :alt="group.name">
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black">{{ group.name }}</h3>

                <div class="flex gap-1">
                    <span v-if="group.role === 'admin'" :title="$t('dearbook.group.list.title.role.admin')">
                        <AdjustmentsHorizontalIcon
                            class="w-[21px] h-[21px] text-cyan-500 rounded-full bg-white p-[0.5px]" />
                    </span>

                    <span v-if="group.status === 'pending'" :title="$t('dearbook.group.list.title.status.pending')">
                        <ShieldExclamationIcon
                            class="w-[21px] h-[21px] text-orange-500 rounded-full bg-white p-[0.5px]" />
                    </span>
                </div>
            </div>
            <div class="text-xs text-gray-500">{{ group.short_description ||
                $t('dearbook.group.list.no_description') }}
            </div>
        </div>
        </Link>
    </div>
</template>
