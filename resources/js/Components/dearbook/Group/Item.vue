<script setup>
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";
import { ShieldExclamationIcon } from "@heroicons/vue/24/outline";

defineProps({
    group: Object,
});
</script>

<template>
    <div class="rounded-md cursor-pointer hover:bg-white">
        <div class="flex items-start gap-2 px-2 py-1">
            <img :src="group.thumbnail_url || '/img/default_thumbnail_group.png'" class="mt-1 w-[36px] rounded-full"
                :alt="group.name">
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
                <div class="text-xs text-gray-500">{{ group.description ||
                    $t('dearbook.group.list.no_description') }}
                </div>
            </div>
        </div>
    </div>
</template>
