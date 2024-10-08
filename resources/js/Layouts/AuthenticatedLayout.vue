<script setup>
import { ArrowLeftStartOnRectangleIcon, UserCircleIcon } from "@heroicons/vue/24/solid";
import { ArchiveBoxIcon } from "@heroicons/vue/24/outline";
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

import ScrollToTop from '@/Components/dearbook/ScrollToTop.vue'

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-[beige] dark:bg-[#b4b440]">
            <nav
                class="sticky top-0 z-50 w-full bg-white border-b border-gray-100 shadow dark:bg-gray-800 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto sm:px-6 lg:px-8">
                    <div class="flex justify-between h-14">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('home')">
                                <ApplicationLogo
                                    class="block w-auto text-gray-800 fill-current h-9 dark:text-gray-200" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <!-- <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                    :title="$t('Dashboard')">
                                    {{ $t('Dashboard') }}
                                </NavLink> -->
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" title="Cuenta"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md group/menu_user dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                                <!-- {{ $page.props.auth.user.name }} -->
                                                <img :src="$page.props.auth.user.avatar_url"
                                                    class="w-10 transition-all border-2 rounded-full group-hover/menu_user:border-cyan-500"
                                                    :alt="$page.props.auth.user.name" />

                                                <svg class="absolute w-4 h-4 bg-gray-200 border border-white rounded-full top-8 right-2 p-o group-hover/menu_user:bg-gray-300"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="p-1.5">
                                            <DropdownLink
                                                :href="route('profile.index', { username: $page.props.auth.user.username })"
                                                :title="$t('Profile')"
                                                class="flex items-center gap-1.5 group/menu_user_profile_item">
                                                <!-- {{ $t('Profile') }} -->
                                                <img :src="$page.props.auth.user.avatar_url"
                                                    class="transition-all border-2 rounded-full w-9 h-9 group-hover/menu_user_profile_item:border-cyan-500"
                                                    :alt="$page.props.auth.user.name" />
                                                <span class="font-bold">{{ $page.props.auth.user.name }}</span>
                                            </DropdownLink>
                                            <DropdownLink class="flex items-center gap-1 border-t border-t-gray-200"
                                                :href="route('archive-management.index')"
                                                :title="$t('dearbook.archive_management.section_label')">
                                                <ArchiveBoxIcon class="w-4 h-4" />
                                                {{ $t('dearbook.archive_management.section_label') }}
                                            </DropdownLink>
                                            <DropdownLink class="flex items-center gap-1 border-t border-t-gray-200"
                                                :href="route('logout')" method="post" as="button"
                                                :title="$t('Log Out')">
                                                <ArrowLeftStartOnRectangleIcon class="w-4 h-4" />
                                                {{ $t('Log Out') }}
                                            </DropdownLink>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center -me-2 sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400">
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
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <!-- <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                            :title="$t('Dashboard')">
                            {{ $t('Dashboard') }}
                        </ResponsiveNavLink> -->
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex px-4">
                            <img :src="$page.props.auth.user.avatar_url"
                                class="w-10 transition-all border-2 rounded-full group-hover:border-cyan-500"
                                :alt="$page.props.auth.user.name" />
                            <div class="ml-4">
                                <div class="text-base font-medium text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink class="flex items-center gap-1"
                                :href="route('profile.index', { username: $page.props.auth.user.username })"
                                :title="$t('Profile')">
                                <UserCircleIcon class="w-4 h-4" />
                                {{ $t('Profile') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink class="flex items-center gap-1" :href="route('archive-management.index')"
                                :title="$t('dearbook.archive_management.section_label')">
                                <ArchiveBoxIcon class="w-4 h-4" />
                                {{ $t('dearbook.archive_management.section_label') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink class="flex items-center gap-1" :href="route('logout')" method="post"
                                as="button" :title="$t('Log Out')">
                                <ArrowLeftStartOnRectangleIcon class="w-4 h-4" />
                                {{ $t('Log Out') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />

                <ScrollToTop />
            </main>
        </div>
    </div>
</template>
