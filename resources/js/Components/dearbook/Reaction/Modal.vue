<script setup>
import { XMarkIcon, } from "@heroicons/vue/24/solid";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    TabGroup, TabList, Tab, TabPanels, TabPanel,
} from "@headlessui/vue";
import TabItem from "@/Components/dearbook/Partials/TabItem.vue";
import UserListItem from '@/Components/dearbook/Reaction/UserListItem.vue'
import { computed } from "vue";
import { reactionTypesFormat } from "@/Libs/helpers";

const props = defineProps({
    entity: Object,
    defaultIndex: {
        type: Number,
        default: 0,
    },
    modelValue: Boolean,
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const showTab = (index) => {
    if (index === 'all') {
        return props.entity[index].length > 0 || props.entity.current_user_has_reaction
    }
    return props.entity[index].length > 0 || (props.entity.current_user_has_reaction && props.entity.current_user_type_reaction == index)
}

const showCurrentUserReaction = (index) => {
    if (index === 'all') {
        return props.entity.current_user_has_reaction
    }
    return props.entity.current_user_has_reaction && props.entity.current_user_type_reaction == index
}

const totalOfReactions = (index) => {
    let total = 0
    total = props.entity[index].length
    if (index === 'all') {
        if (props.entity.current_user_has_reaction) {
            total++
        }
    } else {
        if (props.entity.current_user_has_reaction && props.entity.current_user_type_reaction == index) {
            total++
        }
    }
    return total
}

const emit = defineEmits(["update:modelValue"]);

const closeModal = () => {
    show.value = false;
};
</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-20">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-full p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-[606px] overflow-hidden text-left align-middle transition-all transform bg-white rounded-md shadow-xl"
                            >
                                <div class="">
                                    <TabGroup :defaultIndex="defaultIndex">
                                        <div class="flex items-center justify-between px-2 bg-white shadow md:px-4">
                                            <TabList class="flex">
                                                <template v-for="(reactionType, index) of reactionTypesFormat">
                                                    <Tab v-if="showTab(index)" as="template" v-slot="{ selected }">
                                                        <TabItem v-if="index == 'all'" :text="reactionType.reaction_modal_header_text" :total-of-reactions="totalOfReactions(index)" :show-type-icon="false" :selected="selected" />
                                                        <TabItem v-else :text="reactionType.reaction_modal_header_text" :total-of-reactions="totalOfReactions(index)" :reaction-type="index" :text-color="reactionType.classes" :selected="selected" />
                                                    </Tab>
                                                </template>
                                            </TabList>

                                            <button
                                                @click="closeModal"
                                                class="flex items-center p-1 font-bold text-gray-500 transition-colors duration-200 bg-gray-200 rounded-full hover:bg-gray-300 hover:text-gray-700"
                                                title="Cerrar"
                                            >
                                                <XMarkIcon class="w-5 h-5" />
                                            </button>
                                        </div>

                                        <TabPanels class="overflow-auto h-96">
                                            <template v-for="(reactionType, index) of reactionTypesFormat">
                                                <TabPanel v-if="showTab(index)" class="w-full px-2 py-2 bg-white md:px-4">
                                                    <UserListItem v-if="showCurrentUserReaction(index)" :user="$page.props.auth.user" :type="entity.current_user_type_reaction" :title="reactionTypesFormat[index].main_type_button_text" />

                                                    <UserListItem v-for="element of entity[index]" :user="element.user" :type="element.type" :title="reactionTypesFormat[index].main_type_button_text" />
                                                </TabPanel>
                                            </template>
                                        </TabPanels>
                                    </TabGroup>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
