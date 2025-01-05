<script setup>
import { AdjustmentsHorizontalIcon } from "@heroicons/vue/24/outline";
import { ShieldExclamationIcon } from "@heroicons/vue/24/outline";
import ChiefAdminStarIcon from '@/Components/Icons/Star.vue'
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from "vue";

const props = defineProps({
    group: {
        type: Object,
    },
    isMyProfile: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['callShowConfirmLeaveGroup',])

const authUser = usePage().props.auth.user;

const authUserIsTheOwnerGroup = computed(() => authUser && authUser.id === props.group.user.id)

const handleLeaveGroupClick = (event) => {
    event.preventDefault();
    event.stopPropagation();
    emit('callShowConfirmLeaveGroup', props.group, authUserIsTheOwnerGroup.value);
}
</script>

<template>
    <div class="relative rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 group">
        <Link :href="route('group.profile', { group: group.slug })" class="flex items-start gap-2 px-2 py-1">
        <img :src="group.thumbnail_url"
            class="mt-1 w-[36px] rounded-md border-[1px] border-gray-300 dark:border-gray-400 bg-gray-50 dark:bg-gray-400"
            :alt="group.name">
        <div class="flex-1">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-black">{{ group.name }}</h3>

                <div class="flex gap-1 mt-1">
                    <template v-if="group.role === 'admin'">
                        <span v-if="authUserIsTheOwnerGroup" :title="$t('dearbook.group.list.main.title.owner')">
                            <ChiefAdminStarIcon
                                class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                                fill-content="#fff" />
                        </span>
                        <span v-else :title="$t('dearbook.group.list.main.title.role.admin')">
                            <AdjustmentsHorizontalIcon
                                class="w-[21px] h-[21px] text-cyan-500 rounded-full bg-white p-[0.5px]" />
                        </span>
                    </template>

                    <span v-if="group.status === 'pending'"
                        :title="$t('dearbook.group.list.main.title.status.pending')">
                        <ShieldExclamationIcon
                            class="w-[21px] h-[21px] text-orange-500 rounded-full bg-white p-[0.5px]" />
                    </span>
                </div>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ group.short_description ||
                $t('dearbook.group.list.main.no_description') }}
            </div>
        </div>
        </Link>

        <a v-if="isMyProfile" :href="void 0" @click="handleLeaveGroupClick"
            class="absolute w-full bottom-2 text-sm text-center text-gray-200 bg-red-300 opacity-0 group-hover:opacity-100 hover:bg-red-400 hover:text-white transition-all"
            title="Abandonar grupo">Abandonar</a>
    </div>
</template>
