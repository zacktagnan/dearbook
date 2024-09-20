<script setup>
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';
import PublicAccessIcon from '@/Components/Icons/PublicAccess.vue'
import PrivateAccessIcon from '@/Components/Icons/PrivateAccess.vue'
import HistoryIcon from '@/Components/Icons/History.vue'
import { EyeIcon, } from "@heroicons/vue/24/outline"
import { computed, } from "vue"

const props = defineProps({
    group: {
        type: Object,
    },
});

const maxContentLength = 140
const isPrivateGroup = computed(() => props.group.type === 'private')
</script>

<template>
    <section>
        <div class="w-full bg-white shadow p-4 dark:bg-gray-800 sm:rounded-lg">
            <div>
                <header>
                    <h2 class="px-2 font-semibold text-gray-600 lg:text-lg dark:text-gray-100">
                        {{ $t("dearbook.group.tab_info.info_block.header") }}
                    </h2>
                </header>

                <hr class="mt-3 mb-4 border-gray-400">

                <div class="px-2">
                    <ReadMoreOrLess :content="group.full_description" :max-content-length="maxContentLength" />
                </div>

                <div class="mt-4 px-2 mb-2">
                    <div class="flex gap-3">
                        <div class="mt-1.5">
                            <PrivateAccessIcon v-if="isPrivateGroup" class-content="w-[18px] h-[18px]"
                                fill-content="#9ca3af" />
                            <PublicAccessIcon v-else class-content="w-[18px] h-[18px]" fill-content="#9ca3af" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.tab_info.info_block.features.' + group.type +
                                '.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{ $t('dearbook.group.tab_info.info_block.features.' +
                                group.type +
                                '.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-1.5">
                        <div class="mt-1.5">
                            <EyeIcon class="w-5 h-5 text-gray-400" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.tab_info.info_block.features.visible.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{
                                $t('dearbook.group.tab_info.info_block.features.visible.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-1.5">
                        <div class="mt-1.5">
                            <HistoryIcon class-content="w-5 h-5" fill-content="#9ca3af" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.tab_info.info_block.features.history.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{
                                $t('dearbook.group.tab_info.info_block.features.history.description_1_2', {
                                    'date': group.created_at_formatted
                                }) }} {{
                                    $t('dearbook.group.tab_info.info_block.features.history.description_2_2') }} <a
                                    :href="route('profile.index', { username: group.user.username })"
                                    class="hover:underline font-medium" :title="'Perfil de ' + group.user.name">{{
                                        group.user.name }}</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full bg-white shadow mt-4 p-4 dark:bg-gray-800 sm:rounded-lg">
            <div>
                <header>
                    <h2 class="px-2 font-semibold text-gray-600 lg:text-lg dark:text-gray-100">
                        {{ $t("dearbook.group.tab_info.members_block.header") }} · <span class="text-gray-400">{{
                            group.total_group_user
                            }}</span>
                    </h2>
                </header>

                <hr class="mt-3 mb-4 border-gray-400">

                <div class="px-2">
                    <div class="flex items-center justify-center w-[30px] h-[30px] shadow-lg">
                        <a :href="route('profile.index', { username: group.user.username })"
                            :title="'Perfil de ' + group.user.name">
                            <img :src="group.user.avatar_url || '/img/default_avatar.png'" :alt="group.user.name"
                                class="w-[30px] h-[30px] rounded-full ring-2 ring-white dark:ring-slate-900" />
                        </a>
                    </div>

                    <p class="mt-3 text-sm"><a :href="route('profile.index', { username: group.user.username })"
                            class="hover:underline font-medium" :title="'Perfil de ' + group.user.name">{{
                                group.user.name }}</a> {{ $t("dearbook.group.tab_info.members_block.admin_text") }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
