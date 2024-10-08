<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    post: Object,
    showPostDate: {
        type: Boolean,
        default: true,
    }
})
</script>

<template>
    <div class="flex items-center gap-2">
        <Link :href="route('profile.index', { username: post.user.username })" :title="'Perfil de ' + post.user.name">
        <!-- Clases para marcar usuario activo, por BADGE o por anillo (ring) -->
        <!-- <div class="w-10 rounded-full avatar online ring-2 ring-primary ring-offset-slate-50 ring-offset-1"> -->
        <div class="w-10 rounded-full avatar offline">
            <img :src="post.user.avatar_url" class="w-10 transition-all border-2 rounded-full hover:border-cyan-500"
                :alt="post.user.name" />
        </div>
        </Link>
        <div class="leading-none">
            <h4 class="font-bold">
                <Link :href="route('profile.index', { username: post.user.username })" class="hover:underline"
                    :title="'Perfil de ' + post.user.name">
                {{ post.user.name }}
                </Link>
                <template v-if="post.group">
                    <span class="mx-1 text-cyan-500">></span>
                    <Link :href="route('group.profile', post.group.slug)" class="hover:underline"
                        :title="'Perfil de ' + post.group.name">{{
                            post.group.name }}</Link>
                </template>
            </h4>
            <div v-if="showPostDate" class="flex text-gray-400">
                <div class="tooltip tooltip-right" :data-tip="post.created_at_large_format">
                    <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                        class="text-xs hover:cursor-pointer hover:underline">
                    {{ post.created_at_formatted }}
                    </Link>
                </div>
                <div v-if="post.created_at != post.updated_at">
                    <small class="ml-0.5 text-xs">&bull;</small>
                    <div class="tooltip tooltip-right" :data-tip="post.updated_at_large_format">
                        <small class="ml-0.5 text-xs hover:cursor-pointer italic">Editado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
