<script setup>
import { ArchiveBoxIcon, ArrowUturnLeftIcon, TrashIcon, } from "@heroicons/vue/24/outline";
import TrashedItem from '@/Pages/ArchiveManagement/Partials/Item.vue'

const props = defineProps({
    posts: Object,
});
</script>

<template>
    <div>
        <div class="flex items-center justify-between px-3 py-5 bg-white rounded-lg">
            <div class="flex items-center">
                <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-slate-200">
                    <input type="checkbox" name="check_all" id="check_all"
                        class="w-[22px] h-[22px] rounded group-hover/check_all:bg-slate-200">
                </div>
                <label for="check_all" class="ml-1 font-bold hover:cursor-pointer">Todo</label>
            </div>

            <div class="flex items-center gap-5 pr-2">
                <button class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300">
                    <ArchiveBoxIcon class="w-5 h-5" />
                    Archivar
                </button>
                <button class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300">
                    <ArrowUturnLeftIcon class="w-5 h-5" />
                    Restaurar
                </button>
                <button class="flex items-center gap-1 px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300">
                    <TrashIcon class="w-5 h-5" />
                    Eliminar
                </button>
            </div>
        </div>

        <template v-if="posts.length === 0">
            <div class="px-3 py-4 mt-4 text-center bg-white rounded-lg">
                No hay registros
            </div>
        </template>
        <template v-else>
            <div v-for="(postsPerDay, day) of posts" class="px-3 py-4 mt-4 bg-white rounded-lg">
                <h4 class="ml-1.5 text-lg font-bold">{{ day }} <small>({{ postsPerDay.length }})</small></h4>

                <TrashedItem v-for="(post, index) of postsPerDay" :post="post" :index="index" />
            </div>
        </template>
    </div>
</template>
