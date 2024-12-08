<script setup>
import BaseModal from '@/Components/dearbook/BaseModal.vue'
import CloseModal from '@/Components/dearbook/CloseModal.vue'
import {
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
    <BaseModal v-model="show" :z-index="'z-[55]'" :dialog-panel-extra-classes="'max-w-[606px] dark:bg-slate-800'" @callCloseModal="closeModal">
        <div>
            <TabGroup :defaultIndex="defaultIndex">
                <div class="flex items-center justify-between px-2 bg-white dark:bg-slate-800 shadow md:px-4">
                    <TabList class="flex">
                        <template v-for="(reactionType, index) of reactionTypesFormat">
                            <Tab v-if="showTab(index)" as="template" v-slot="{ selected }">
                                <TabItem v-if="index == 'all'" :text="reactionType.reaction_modal_header_text" :total-of-reactions="totalOfReactions(index)" :show-type-icon="false" :selected="selected" />
                                <TabItem v-else :text="reactionType.reaction_modal_header_text" :total-of-reactions="totalOfReactions(index)" :reaction-type="index" :text-color="reactionType.classes" :selected="selected" />
                            </Tab>
                        </template>
                    </TabList>

                    <CloseModal @call-close-modal="closeModal" />
                </div>

                <TabPanels class="overflow-auto h-96">
                    <template v-for="(reactionType, index) of reactionTypesFormat">
                        <TabPanel v-if="showTab(index)" class="w-full px-2 py-2 bg-white dark:bg-slate-800 md:px-4">
                            <UserListItem v-if="showCurrentUserReaction(index)" :user="$page.props.auth.user" :type="entity.current_user_type_reaction" :title="reactionTypesFormat[index].main_type_button_text" />

                            <UserListItem v-for="element of entity[index]" :user="element.user" :type="element.type" :title="reactionTypesFormat[index].main_type_button_text" />
                        </TabPanel>
                    </template>
                </TabPanels>
            </TabGroup>
        </div>
    </BaseModal>
</template>
