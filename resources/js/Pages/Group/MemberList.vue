<script setup>
import TextInput from '@/Components/TextInput.vue'
import UserItem from '@/Components/dearbook/User/Item.vue'
import ChiefAdminStarIcon from '@/Components/Icons/Star.vue'
import { XMarkIcon } from "@heroicons/vue/24/solid";
import { ref, watch } from 'vue'

const props = defineProps({
    groupUserId: Number,
    members: Array,
    authUser: Object,
    authUserIsTheOwnerGroup: {
        type: Boolean,
        default: false,
    },
    isAdminGroup: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['callMemberRoleChange', 'callConfirmDeleteMemberModal'])

const isTheOwnerGroup = (memberId) => props.groupUserId === memberId

const membersCollection = ref(props.members)
const searchKeyword = ref('')

watch(() => props.members, (newMembers) => {
    membersCollection.value = [...newMembers];
}, { immediate: true });

const isTypingTerm = ref(false)
let typingTermTimeout
const isFiltering = ref(false)

const onTypingTerm = () => {
    isTypingTerm.value = true

    clearTimeout(typingTermTimeout)
    typingTermTimeout = setTimeout(() => {
        isTypingTerm.value = false
    }, 500)
}

const filterList = () => {
    isTypingTerm.value = false
    isFiltering.value = true

    if (searchKeyword.value.trim() === '') {
        membersCollection.value = props.members;
    } else {
        membersCollection.value = props.members.filter(member =>
            member.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
            || member.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        );
    }
}

const clearFilter = () => {
    searchKeyword.value = ''
    membersCollection.value = props.members
    isFiltering.value = false
}
</script>

<template>
    <div class="relative">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.group.search.inside_profile.placeholder')"
            @keyup="onTypingTerm"
            @keyup.enter="filterList" />

        <button @click="clearFilter" class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150" :class="[
            isFiltering
            ? ''
            : 'hidden'
        ]" :title="$t('dearbook.group.search.inside_profile.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <div class="text-gray-400 text-xs pl-3 h-3">
        <p v-if="isTypingTerm" class="italic">{{ $t('dearbook.group.search.inside_profile.writing') }}</p>
        <p v-if="!isTypingTerm && searchKeyword">{{ $t('dearbook.group.search.inside_profile.push_enter_to_filter') }}</p>
    </div>

    <div v-if="membersCollection.length" class="grid gap-3 mt-3">
        <UserItem v-for="member of membersCollection" :user="member"
            :classes="' shadow shadow-gray-200 hover:shadow-gray-400 hover:bg-gray-50'"
            :key="member.id" :user-since-date="member.joining_date">
            <div v-if="isAdminGroup" class="flex items-center gap-2">
                <select v-if="authUserIsTheOwnerGroup" @change="$emit('callMemberRoleChange', member, $event.target.value)"
                    :disabled="isTheOwnerGroup(member.id)"
                    class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400">
                    <option value="admin" :selected="member.role === 'admin'">admin</option>
                    <option value="user" :selected="member.role === 'user'">user</option>
                </select>

                <template v-if="authUser.id === member.id && isTheOwnerGroup(member.id)">
                    <!-- <button class="bg-red-300 rounded-full p-1 hover:bg-red-500 group/btn_del_member disabled:bg-red-100"
                    :title="[
                        isTheOwnerGroup(member.id)
                        ? 'Imposible eliminar creador del grupo'
                        : 'Eliminar miembro'
                    ]"
                    :disabled="isTheOwnerGroup(member.id)"
                    @click="showConfirmDeleteMemberModal(member)">
                        <XMarkIcon class="w-4 h-4 text-gray-100 group-hover/btn_del_member:text-white group-disabled/btn_del_member:text-white" />
                    </button> -->
                    <div class="rounded-full bg-cyan-600 p-0.5" title="Administrador Jefe">
                        <ChiefAdminStarIcon class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5" fill-content="#fff" />
                    </div>
                </template>
                <template v-else>
                    <button v-if="authUser.id !== member.id && !isTheOwnerGroup(member.id)" class="bg-red-300 rounded-full p-1 hover:bg-red-500 group/btn_del_member disabled:bg-red-300"
                    :title="[
                        isTheOwnerGroup(member.id)
                        ? 'Imposible eliminar creador del grupo'
                        : 'Eliminar miembro'
                    ]"
                    :disabled="isTheOwnerGroup(member.id)"
                    @click="$emit('callConfirmDeleteMemberModal', member)">
                        <XMarkIcon class="w-4 h-4 text-gray-100 group-hover/btn_del_member:text-white group-disabled/btn_del_member:text-gray-100" />
                    </button>
                    <div v-else-if="isTheOwnerGroup(member.id)" class="rounded-full bg-cyan-600 p-0.5" title="Administrador Jefe">
                        <ChiefAdminStarIcon class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5" fill-content="#fff" />
                    </div>
                </template>
            </div>
            <div v-else-if="isTheOwnerGroup(member.id)" class="rounded-full bg-cyan-600 p-0.5" title="Administrador Jefe">
                <ChiefAdminStarIcon class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5" fill-content="#fff" />
            </div>
        </UserItem>
    </div>
    <div v-else>
        <p v-if="isFiltering" class="w-full text-center mt-3">
            {{ $t('dearbook.group.search.inside_profile.no_registers') }}
        </p>
        <p v-else class="w-full text-center mt-3">
            {{ $t('dearbook.group.list.members.no_registers') }}
        </p>
    </div>
</template>
