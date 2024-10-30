<script setup>
import ReadMoreOrLess from '@/Components/dearbook/ReadMoreOrLess.vue';
import PublicAccessIcon from '@/Components/Icons/PublicAccess.vue'
import PrivateAccessIcon from '@/Components/Icons/PrivateAccess.vue'
import HistoryIcon from '@/Components/Icons/History.vue'
import { EyeIcon, } from "@heroicons/vue/24/outline"
import { computed, } from "vue"
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    group: {
        type: Object,
    },
});

const user = usePage().props.auth.user;

const maxContentLength = 140
const isPrivateGroup = computed(() => props.group.type === 'private')
</script>

<template>
    <section>
        <div class="w-full bg-white shadow p-4 dark:bg-gray-800 sm:rounded-lg">
            <div>
                <header>
                    <h2 class="px-2 font-semibold text-gray-600 lg:text-lg dark:text-gray-100">
                        {{ $t("dearbook.group.index.tab_info.info_block.header") }}
                    </h2>
                </header>

                <hr class="mt-3 mb-4 border-gray-400">

                <div class="px-2">
                    <ReadMoreOrLess v-if="group.full_description && group.full_description.length > 0"
                        :content="group.full_description" :max-content-length="maxContentLength"
                        :showing-banner-if-content-is-null="false" />
                    <div v-else-if="!group.full_description" class="text-sm italic"
                        v-html="$t('dearbook.group.index.tab_info.info_block.no_intro')" />
                </div>

                <div class="mt-4 px-2 mb-2">
                    <div class="flex gap-3">
                        <div class="mt-1 md:mt-1.5">
                            <PrivateAccessIcon v-if="isPrivateGroup" class-content="w-[18px] h-[18px]"
                                fill-content="#9ca3af" />
                            <PublicAccessIcon v-else class-content="w-[18px] h-[18px]" fill-content="#9ca3af" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.index.tab_info.info_block.features.' + group.type +
                                '.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{ $t('dearbook.group.index.tab_info.info_block.features.' +
                                group.type +
                                '.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-1.5">
                        <div class="mt-0.5 md:mt-1">
                            <EyeIcon class="w-5 h-5 text-gray-400" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.index.tab_info.info_block.features.visible.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{
                                $t('dearbook.group.index.tab_info.info_block.features.visible.description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-1.5">
                        <div class="mt-1 md:mt-1.5">
                            <HistoryIcon class-content="w-5 h-5" fill-content="#9ca3af" />
                        </div>
                        <div class="flex-col">
                            <h3 class="lg:text-lg">{{ $t('dearbook.group.index.tab_info.info_block.features.history.title') }}
                            </h3>
                            <p class="text-[13px] leading-none">{{
                                $t('dearbook.group.index.tab_info.info_block.features.history.description.1_3', {
                                    'date': group.created_at_formatted
                                }) }} {{
                                    $t('dearbook.group.index.tab_info.info_block.features.history.description.2_3') }} <a
                                    :href="route('profile.index', { username: group.user.username })"
                                    class="hover:underline font-medium" :title="$t('Profile of', {
                                        'name': group.user.name
                                    })">
                                    <span v-if="group.user.id === user.id">{{
                                        $t('dearbook.group.index.tab_info.info_block.features.history.description.3_3')
                                        }}</span>
                                    <span v-else>{{ group.user.name }}</span>
                                </a>.
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
                        {{ $t("dearbook.group.index.tab_info.members_block.header") }} · <span class="text-gray-400">{{
                            group.total_group_user
                            }}</span>
                    </h2>
                </header>

                <hr class="mt-3 mb-4 border-gray-400">

                <div class="px-2">
                    <div class="flex items-center justify-center w-8 h-8 shadow-lg rounded-full">
                        <a :href="route('profile.index', { username: group.user.username })"
                            :title="'Perfil de ' + group.user.name">
                            <img :src="group.user.avatar_url" :alt="group.user.name"
                                class="w-8 h-8 rounded-full border-[1px] border-gray-200 hover:border-gray-400 transition-colors delay-150" />
                        </a>
                    </div>

                    <p class="mt-3 text-sm"><a :href="route('profile.index', { username: group.user.username })"
                            class="hover:underline font-medium" :title="'Perfil de ' + group.user.name">{{
                                group.user.name }}</a> {{ $t("dearbook.group.index.tab_info.members_block.admin_text") }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
