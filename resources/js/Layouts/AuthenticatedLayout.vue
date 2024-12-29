<script setup>
import { ArrowLeftStartOnRectangleIcon, UserCircleIcon, MoonIcon, SunIcon } from "@heroicons/vue/24/solid";
import { ArchiveBoxIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router } from '@inertiajs/vue3';

import ScrollToTop from '@/Components/dearbook/ScrollToTop.vue'
import TextInput from "@/Components/TextInput.vue";

const showingNavigationDropdown = ref(false);
const keywords = ref('')

import { usePage } from "@inertiajs/vue3"
const { errors } = usePage().props

const globalSearch = () => {
    router.get(route('global-search', encodeURIComponent(keywords.value)))
}

const isDarkMode = ref(parseInt(localStorage.getItem('darkMode') || 0))
const toggleDarkMode = () => {
    const html = window.document.documentElement
    if (html.classList.contains('dark')) {
        html.classList.remove('dark')
        isDarkMode.value = false
        localStorage.setItem('darkMode', '0')
    } else {
        html.classList.add('dark')
        isDarkMode.value = true
        localStorage.setItem('darkMode', '1')
    }
}
</script>

<template>
    <div>
        <div class="min-h-screen">
            <nav
                class="sticky top-0 z-50 w-full bg-white border-b border-gray-100 shadow dark:shadow-gray-600 dark:bg-gray-800 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto sm:px-6 lg:px-8">
                    <div class="flex justify-between h-14 items-center">
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

                        <div class="w-full flex justify-center items-center gap-2">
                            <TextInput v-model="keywords" @keyup.enter="globalSearch" :placeholder="errors.keywords
                                ? errors.keywords
                                : 'Usuarios, grupos o publicaciones'
                                " class="placeholder:italic w-2/3 md:w-1/2" :class="[
                                    errors.keywords
                                        ? 'placeholder:text-red-400 dark:placeholder:text-red-400'
                                        : 'placeholder:text-gray-300 dark:placeholder:text-gray-600'
                                ]" />

                            <!-- <button @click="toggleDarkMode"
                                class="text-sky-200 hover:text-sky-300 dark:text-yellow-200 dark:hover:text-yellow-300 transition-colors duration-150"
                                :title="[
                                    isDarkMode
                                    ? 'Cambiar al modo claro'
                                    : 'Cambiar al modo oscuro'
                                ]"
                            >
                                <SunIcon v-if="isDarkMode" class="size-8" />
                                <MoonIcon v-else class="size-6" />
                            </button> -->

                            <label class="swap swap-rotate group" :title="[
                                isDarkMode
                                    ? 'Cambiar al modo claro'
                                    : 'Cambiar al modo oscuro'
                            ]">
                                <!-- this hidden checkbox controls the state -->
                                <input type="checkbox" class="hidden" :checked="isDarkMode" @change="toggleDarkMode" />

                                <!-- sun icon -->
                                <svg class="swap-on size-7 fill-yellow-100 group-hover:fill-yellow-200 transition-all duration-300"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path
                                        d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                                </svg>

                                <!-- moon icon -->
                                <svg class="swap-off size-7 fill-sky-200 group-hover:fill-sky-300 transition-all duration-300"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path
                                        d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                                </svg>
                            </label>
                        </div>

                        <div class="hidden sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" title="Cuenta"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md group/menu_user dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                                <!-- {{ $page.props.auth.user.name }} -->
                                                <img :src="$page.props.auth.user.avatar_url"
                                                    class="w-10 transition-all border-2 rounded-full group-hover/menu_user:border-cyan-500 bg-gray-100 dark:bg-gray-200"
                                                    :alt="$page.props.auth.user.name" />

                                                <svg class="absolute w-4 h-4 bg-gray-200 border border-white rounded-full top-8 right-2 p-o group-hover/menu_user:bg-gray-300 dark:group-hover/menu_user:bg-gray-900"
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
                                                    class="transition-all border-2 rounded-full w-9 h-9 group-hover/menu_user_profile_item:border-cyan-500 bg-gray-100 dark:bg-gray-200"
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
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400">
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
                                class="w-10 transition-all border-2 rounded-full group-hover:border-cyan-500 bg-gray-100 dark:bg-gray-200"
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
