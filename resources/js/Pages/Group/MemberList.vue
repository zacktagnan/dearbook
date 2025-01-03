<script setup>
import TextInput from '@/Components/TextInput.vue'
import UserItem from '@/Components/dearbook/User/Item.vue'
import ChiefAdminStarIcon from '@/Components/Icons/Star.vue'
import { XMarkIcon, NoSymbolIcon, UserGroupIcon, UserIcon } from "@heroicons/vue/24/solid";
import { ref, watch } from 'vue'

const props = defineProps({
    groupUserId: Number,
    isThatCreatorAndOwnerNotTheSame: {
        type: Boolean,
        default: false,
    },
    creatorAndOwner: Object,
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
    isCreatorMemberGroup: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['callMemberRoleChange', 'callConfirmDeleteMemberModal'])

const creatorAndOwnerCollection = ref(props.creatorAndOwner)

const isTheOwnerGroup = (memberId) => props.groupUserId === memberId

const membersCollection = ref(props.members)
const searchKeyword = ref('')

watch(() => props.members, (newMembers) => {
    membersCollection.value = [...newMembers];
}, { immediate: true });
watch(() => props.creatorAndOwner, (newVal) => {
    creatorAndOwnerCollection.value = newVal;
}, { immediate: true })

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
        membersCollection.value = props.members
        creatorAndOwnerCollection.value = props.creatorAndOwner
    } else {
        membersCollection.value = props.members.filter(member =>
            member.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
            || member.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        )

        const filteredCreatorAndOwner = {};

        // Filtrar 'creator'
        if (props.creatorAndOwner.creator && (
            props.creatorAndOwner.creator.name.toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
            props.creatorAndOwner.creator.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        )) {
            filteredCreatorAndOwner.creator = props.creatorAndOwner.creator;
        }

        // Filtrar 'owner'
        if (props.creatorAndOwner.owner && (
            props.creatorAndOwner.owner.name.toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
            props.creatorAndOwner.owner.username.toLowerCase().includes(searchKeyword.value.toLowerCase())
        )) {
            filteredCreatorAndOwner.owner = props.creatorAndOwner.owner;
        }

        // Asignar el objeto filtrado a la colección reactiva
        creatorAndOwnerCollection.value = filteredCreatorAndOwner
    }
}

const clearFilter = () => {
    searchKeyword.value = ''
    membersCollection.value = props.members
    creatorAndOwnerCollection.value = props.creatorAndOwner
    isFiltering.value = false
}

const filterDeletedMember = (memberId) => {
    // console.log(`Tras eliminar el Miembro de ID [${memberId}], será filtrado de la lista de miembros.`)
    membersCollection.value = props.members.filter(m => m.id != memberId)
}

defineExpose({
    filterDeletedMember,
})
</script>

<template>
    <div class="relative">
        <TextInput class="w-full" v-model="searchKeyword"
            :placeholder="$t('dearbook.group.search.inside_profile.placeholder')" @keyup="onTypingTerm"
            @keyup.enter="filterList" />

        <button @click="clearFilter"
            class="absolute inset-y-0 right-3 bg-red-300 hover:bg-red-400 rounded my-3 text-white transition-colors delay-150"
            :class="[
                isFiltering
                    ? ''
                    : 'hidden'
            ]" :title="$t('dearbook.group.search.inside_profile.remove_filter')">
            <XMarkIcon class="w-4 h-4" />
        </button>
    </div>

    <div class="text-gray-400 text-xs pl-3 h-3">
        <p v-if="isTypingTerm" class="italic">{{ $t('dearbook.group.search.inside_profile.writing') }}</p>
        <p v-if="!isTypingTerm && searchKeyword">{{ $t('dearbook.group.search.inside_profile.push_enter_to_filter') }}
        </p>
    </div>

    <template v-if="isThatCreatorAndOwnerNotTheSame">
        <template v-for="(user, type) in creatorAndOwnerCollection" :key="type">
            <UserItem :user="user"
                :classes="' shadow shadow-gray-200 dark:shadow-gray-50 hover:shadow-gray-400 dark:hover:shadow-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 mt-3'"
                :user-since-date="user.joining_date">
                <div v-if="isAdminGroup" class="flex items-center gap-2">
                    <template v-if="type === 'creator'">
                        <template v-if="user.username && isCreatorMemberGroup">
                            <select v-if="authUserIsTheOwnerGroup"
                                @change="$emit('callMemberRoleChange', user, $event.target.value)"
                                :disabled="isTheOwnerGroup(user.id)"
                                class="rounded-md border-0 py-1 text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400 disabled:dark:text-gray-500 dark:bg-gray-800 hover:bg-sky-100 dark:hover:bg-slate-700">
                                <option value="admin" class="hover:bg-red-400" :selected="user.role === 'admin'">admin
                                </option>
                                <option value="user" :selected="user.role === 'user'">user</option>
                            </select>
                            <template v-else>
                                <span v-if="user.role === 'admin'"
                                    class="px-2 rounded text-sm text-gray-900 dark:text-gray-200 border border-gray-800 dark:border-gray-300 bg-sky-100 dark:bg-slate-700">{{
                                        user.role }}</span>
                            </template>
                        </template>
                    </template>
                    <template v-else>
                        <select v-if="authUserIsTheOwnerGroup"
                            @change="$emit('callMemberRoleChange', user, $event.target.value)"
                            :disabled="isTheOwnerGroup(user.id)"
                            class="rounded-md border-0 py-1 text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400 disabled:dark:text-gray-500 dark:bg-gray-800 hover:bg-sky-100 dark:hover:bg-slate-700">
                            <option value="admin" class="hover:bg-red-400" :selected="user.role === 'admin'">admin
                            </option>
                            <option value="user" :selected="user.role === 'user'">user</option>
                        </select>
                        <template v-else>
                            <span v-if="user.role === 'admin'"
                                class="px-2 rounded text-sm text-gray-900 dark:text-gray-200 border border-gray-800 dark:border-gray-300 bg-sky-100 dark:bg-slate-700">{{
                                    user.role }}</span>
                        </template>
                    </template>

                    <template v-if="authUser.id === user.id && isTheOwnerGroup(user.id)">
                        <div class="rounded-full bg-cyan-600 p-0.5" title="Administrador Jefe">
                            <ChiefAdminStarIcon
                                class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                                fill-content="#fff" />
                        </div>
                    </template>
                    <template v-else>
                        <template v-if="authUser.id !== user.id && !isTheOwnerGroup(user.id)">
                            <template v-if="type === 'creator'">
                                <button v-if="user.username && isCreatorMemberGroup"
                                    class="bg-red-300 rounded-full p-1 hover:bg-red-500 group/btn_del_member disabled:bg-red-300"
                                    :title="[
                                        isTheOwnerGroup(user.id)
                                            ? 'Imposible eliminar creador del grupo'
                                            : 'Eliminar miembro'
                                    ]" :disabled="isTheOwnerGroup(user.id)"
                                    @click="$emit('callConfirmDeleteMemberModal', user)">
                                    <XMarkIcon
                                        class="w-4 h-4 text-gray-100 group-hover/btn_del_member:text-white group-disabled/btn_del_member:text-gray-100" />
                                </button>

                                <template v-else>
                                    <div v-if="!user.username" class="relative cursor-pointer"
                                        title="Ya no tiene cuenta">
                                        <UserIcon class="size-4 text-slate-400 dark:text-slate-300 mt-0.5 mx-0.5" />
                                        <span
                                            class="text-red-400 rotate-45 font-black text-xl absolute bottom-[-5px] left-[7px]">|</span>
                                    </div>
                                    <div v-else class="relative cursor-pointer" title="Ya no es miembro">
                                        <UserGroupIcon
                                            class="size-4 text-slate-400 dark:text-slate-300 mt-0.5 mx-0.5" />
                                        <span
                                            class="text-red-400 rotate-45 font-black text-xl absolute bottom-[-5px] left-[7px]">|</span>
                                    </div>
                                </template>
                            </template>
                            <template v-else>
                                <button
                                    class="bg-red-300 rounded-full p-1 hover:bg-red-500 group/btn_del_member disabled:bg-red-300"
                                    :title="[
                                        isTheOwnerGroup(user.id)
                                            ? 'Imposible eliminar creador del grupo'
                                            : 'Eliminar miembro'
                                    ]" :disabled="isTheOwnerGroup(user.id)"
                                    @click="$emit('callConfirmDeleteMemberModal', user)">
                                    <XMarkIcon
                                        class="w-4 h-4 text-gray-100 group-hover/btn_del_member:text-white group-disabled/btn_del_member:text-gray-100" />
                                </button>
                            </template>
                        </template>
                        <div v-else-if="isTheOwnerGroup(user.id)" class="rounded-full bg-cyan-600 p-0.5"
                            title="Administrador Jefe">
                            <ChiefAdminStarIcon
                                class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                                fill-content="#fff" />
                        </div>
                        <div v-else class="size-6" />
                    </template>
                </div>
                <div v-else>
                    <div v-if="isTheOwnerGroup(user.id)" class="rounded-full bg-cyan-600 p-0.5"
                        title="Administrador Jefe">
                        <ChiefAdminStarIcon
                            class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                            fill-content="#fff" />
                    </div>
                    <template v-else>
                        <span v-if="user.role === 'admin'"
                            class="px-2 rounded text-sm text-gray-900 dark:text-gray-200 border border-gray-800 dark:border-gray-300 bg-sky-100 dark:bg-slate-700">{{
                                user.role }}</span>
                        <template v-else>
                            <template v-if="!user.username">
                                <div class="relative cursor-pointer" title="Ya no tiene cuenta">
                                    <UserIcon class="size-4 text-slate-400 dark:text-slate-300 mt-0.5 mx-0.5" />
                                    <span
                                        class="text-red-400 rotate-45 font-black text-xl absolute bottom-[-5px] left-[7px]">|</span>
                                </div>
                            </template>
                            <template v-else>
                                <div v-if="type === 'creator' && !isCreatorMemberGroup" class="relative cursor-pointer"
                                    title="Ya no es miembro">
                                    <UserGroupIcon class="size-4 text-slate-400 dark:text-slate-300 mt-0.5 mx-0.5" />
                                    <span
                                        class="text-red-400 rotate-45 font-black text-xl absolute bottom-[-5px] left-[7px]">|</span>
                                </div>
                            </template>
                        </template>
                    </template>
                </div>
            </UserItem>
        </template>
    </template>
    <div v-if="membersCollection.length || isThatCreatorAndOwnerNotTheSame" class="grid gap-3 mt-3">
        <UserItem v-for="member of membersCollection" :user="member"
            :classes="' shadow shadow-gray-200 dark:shadow-gray-50 hover:shadow-gray-400 dark:hover:shadow-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900'"
            :key="member.id" :user-since-date="member.joining_date">
            <div v-if="isAdminGroup" class="flex items-center gap-2">
                <select v-if="authUserIsTheOwnerGroup"
                    @change="$emit('callMemberRoleChange', member, $event.target.value)"
                    :disabled="isTheOwnerGroup(member.id)"
                    class="rounded-md border-0 py-1 text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6 disabled:text-gray-400 disabled:dark:text-gray-500 dark:bg-gray-800 hover:bg-sky-100 dark:hover:bg-slate-700">
                    <option value="admin" class="hover:bg-red-400" :selected="member.role === 'admin'">admin</option>
                    <option value="user" :selected="member.role === 'user'">user</option>
                </select>
                <template v-else>
                    <span v-if="member.role === 'admin'"
                        class="px-2 rounded text-sm text-gray-900 dark:text-gray-200 border border-gray-800 dark:border-gray-300 bg-sky-100 dark:bg-slate-700">{{
                            member.role }}</span>
                </template>

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
                        <ChiefAdminStarIcon
                            class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                            fill-content="#fff" />
                    </div>
                </template>
                <template v-else>
                    <button v-if="authUser.id !== member.id && !isTheOwnerGroup(member.id)"
                        class="bg-red-300 rounded-full p-1 hover:bg-red-500 group/btn_del_member disabled:bg-red-300"
                        :title="[
                            isTheOwnerGroup(member.id)
                                ? 'Imposible eliminar creador del grupo'
                                : 'Eliminar miembro'
                        ]" :disabled="isTheOwnerGroup(member.id)"
                        @click="$emit('callConfirmDeleteMemberModal', member)">
                        <XMarkIcon
                            class="w-4 h-4 text-gray-100 group-hover/btn_del_member:text-white group-disabled/btn_del_member:text-gray-100" />
                    </button>
                    <div v-else-if="isTheOwnerGroup(member.id)" class="rounded-full bg-cyan-600 p-0.5"
                        title="Administrador Jefe">
                        <ChiefAdminStarIcon
                            class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                            fill-content="#fff" />
                    </div>
                    <div v-else class="size-6" />
                </template>
            </div>
            <div v-else>
                <div v-if="isTheOwnerGroup(member.id)" class="rounded-full bg-cyan-600 p-0.5"
                    title="Administrador Jefe">
                    <ChiefAdminStarIcon
                        class-content="w-[21px] h-[21px] border-2 border-white bg-cyan-600 rounded-full pb-0.5"
                        fill-content="#fff" />
                </div>
                <template v-else>
                    <span v-if="member.role === 'admin'"
                        class="px-2 rounded text-sm text-gray-900 dark:text-gray-200 border border-gray-800 dark:border-gray-300 bg-sky-100 dark:bg-slate-700">{{
                            member.role }}</span>
                </template>
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
