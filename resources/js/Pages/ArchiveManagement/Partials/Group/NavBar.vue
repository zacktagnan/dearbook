<script setup>
import { ArchiveBoxIcon, ArrowUturnLeftIcon, TrashIcon, XMarkIcon } from "@heroicons/vue/24/outline"
import { ref } from 'vue'

defineProps({
    buttonDisabled: Boolean,
    checkedIdsLength: Number,
    managementType: String,
})

const emit = defineEmits(['callGetComputedButtonDisabled', 'callGetCheckedIdsLength', 'callCheckAll', 'callUnMarkAll', 'callSubmitGlobalProcess'])

const checkAllRef = ref(null)

defineExpose({ checkAllRef, })
</script>

<template>
    <div class="flex items-center justify-between px-3 py-5 bg-white dark:bg-gray-800 dark:text-gray-100 rounded-lg">
        <div class="flex items-center">
            <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-sky-300">
                <input type="checkbox" ref="checkAllRef" @change="$emit('callCheckAll')" name="check_all" id="check_all"
                    class="w-[22px] h-[22px] rounded group-hover/check_all:bg-sky-300 checked:bg-[#0099ce] focus:checked:bg-[#0099ce] focus:ring-0 focus:ring-offset-0">
            </div>
            <label for="check_all" class="ml-1 font-bold hover:cursor-pointer">Todo</label>
            <button v-if="!buttonDisabled" title="Desmarcar toda casilla marcada" @click="$emit('callUnMarkAll')"
                class="ml-3 md:ml-5 flex items-center md:gap-1 pl-2 pr-2 md:pr-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600">
                <XMarkIcon class="w-5 h-5" />
                <span class="hidden md:block">Desmarcar</span>
            </button>
        </div>

        <div v-if="checkedIdsLength > 0">
            [ <span class="font-black text-[#0099ce]">{{ checkedIdsLength }}</span> ]
        </div>

        <div class="flex items-center gap-4 md:gap-5 pr-2">
            <template v-if="managementType === 'archive'">
                <button @click="$emit('callSubmitGlobalProcess', 'restore_from_archive_all_selected')"
                    :disabled="buttonDisabled" :title="!buttonDisabled ? 'Restaurar seleccionado(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <ArrowUturnLeftIcon class="w-5 h-5" />
                    <span class="hidden md:block">Restaurar</span>
                </button>
                <button @click="$emit('callSubmitGlobalProcess', 'delete_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Mover a la papelera los elegido(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <TrashIcon class="w-5 h-5" />
                    <span class="hidden md:block">Mover a la papelera</span>
                </button>
            </template>
            <template v-else-if="managementType === 'trash'">
                <!-- <button @click="$emit('callSubmitGlobalProcess', 'archive_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Archivar seleccionado(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <ArchiveBoxIcon class="w-5 h-5" />
                    <span class="hidden md:block">Archivar</span>
                </button> -->
                <button @click="$emit('callSubmitGlobalProcess', 'restore_from_trash_all_selected')"
                    :disabled="buttonDisabled" :title="!buttonDisabled ? 'Restaurar seleccionado(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <ArrowUturnLeftIcon class="w-5 h-5" />
                    <span class="hidden md:block">Restaurar</span>
                </button>
                <!-- <button @click="$emit('callSubmitGlobalProcess', 'force_delete_all_selected')"
                    :disabled="buttonDisabled" :title="!buttonDisabled ? 'Eliminar seleccionado(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <TrashIcon class="w-5 h-5" />
                    <span class="hidden md:block">Eliminar</span>
                </button> -->
            </template>
            <template v-else>
                <button @click="$emit('callSubmitGlobalProcess', 'archive_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Mover al archivo los elegido(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <ArchiveBoxIcon class="w-5 h-5" />
                    <span class="hidden md:block">Mover al archivo</span>
                </button>
                <button @click="$emit('callSubmitGlobalProcess', 'delete_all_selected')" :disabled="buttonDisabled"
                    :title="!buttonDisabled ? 'Mover a la papelera los elegido(s)' : ''"
                    class="flex items-center md:gap-1 px-2 md:px-3 py-2 font-bold rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:bg-slate-100 disabled:text-gray-400 dark:disabled:bg-slate-500 dark:disabled:text-gray-800">
                    <TrashIcon class="w-5 h-5" />
                    <span class="hidden md:block">Mover a la papelera</span>
                </button>
            </template>
        </div>
    </div>
</template>
