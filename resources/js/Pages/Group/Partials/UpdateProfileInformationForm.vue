<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue'
import Radiobutton from '@/Components/Radiobutton.vue'
import TextareaInput from '@/Components/TextareaInput.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    group: {
        type: Object,
    },
})

const emit = defineEmits(["callActiveShowNotification"])

const user = usePage().props.auth.user;

const groupForm = useForm({
    name: props.group.name,
    auto_approval: props.group.auto_approval === 1 ? true : false,
    type: props.group.type,
    about: props.group.full_description,
    // _method: "POST",
})

const autoApprovalGroupRef = ref(null)
const autoApprovalDisabled = ref(false)

const autoApprovalToFalseAndDisable = (disable) => {
    autoApprovalDisabled.value = !disable
    if (autoApprovalDisabled.value) {
        groupForm.auto_approval = false
    }
}

const submitUpdate = () => {
    groupForm.patch(route('group.update', props.group), {
        preserveScroll: true,
        onSuccess: () => {
            emit("callActiveShowNotification")
        },
        // onFinish: () => form.reset('password'),
    })
}

const reset = () => {
    groupForm.reset()
    autoApprovalDisabled.value = false
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{
                $t('dearbook.group.index.tab_info.info_block.header') }}</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $t("dearbook.group.index.tab_info.info_block.intro") }}
            </p>
        </header>

        <form @submit.prevent="submitUpdate" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" :value="$t('dearbook.group.form.create.fields.name.label')" />

                <TextInput id="name" type="text" class="block w-full mt-1" v-model="groupForm.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="groupForm.errors.name || groupForm.errors.name_unique" />
            </div>

            <div class="flex flex-col md:flex-row items-center md:justify-center gap-4 md:gap-14 lg:gap-28">
                <div class="flex items-center gap-3">
                    <InputLabel for="auto_approval" :disabled="autoApprovalDisabled" :value="$t('dearbook.group.form.create.fields.auto_approval.label')" />
                    <Checkbox ref="autoApprovalGroupRef" id="auto_approval" class="disabled:bg-gray-100 dark:disabled:bg-gray-600" :disabled="autoApprovalDisabled" v-model:checked="groupForm.auto_approval" />
                </div>

                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-3">
                        <InputLabel for="type_1" :value="$t('dearbook.group.form.create.fields.type.label.public')" />
                        <Radiobutton id="type_1" @change="autoApprovalToFalseAndDisable(true)" v-model:checked="groupForm.type" :value="'public'" />
                    </div>

                    <div class="flex items-center gap-3">
                        <InputLabel for="type_2" :value="$t('dearbook.group.form.create.fields.type.label.private')" />
                        <Radiobutton id="type_2" @change="autoApprovalToFalseAndDisable(false)" v-model:checked="groupForm.type" :value="'private'" />
                    </div>
                </div>
            </div>

            <div>
                <InputLabel for="type_1" :value="$t('dearbook.group.form.create.fields.about.label')" />
                <TextareaInput v-model="groupForm.about" :class="'block w-full max-h-28'" rows="2" />

                <InputError class="mt-2" :message="groupForm.errors.about" />
            </div>

            <div class="flex flex-col md:flex-row md:justify-between mx-auto md:mx-0 w-3/4 md:w-full">
                <div class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <div class="font-medium">{{ $t('dearbook.group.form.create.extra.date_of_creation') }}</div>
                    <div>{{ group.created_at_formatted }}</div>
                </div>

                <div class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <div class="font-medium">{{ $t('dearbook.group.form.create.extra.created_by') }}</div>
                    <div>
                        <a :href="route('profile.index', { username: group.user.username })"
                            class="hover:underline font-medium" :title="$t('Profile of', {
                                'name': group.user.name
                            })">
                            <span v-if="group.user.id === user.id">{{
                                $t('dearbook.group.index.tab_info.info_block.features.history.description.3_3')
                            }}</span>
                            <span v-else>{{ group.user.name }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="groupForm.processing" :title="$t('dearbook.group.form.create.btn_init_update')">{{
                    $t('dearbook.group.form.create.btn_init_update') }}</PrimaryButton>
                <SecondaryButton @click="reset" :disabled="groupForm.processing" :title="$t('dearbook.group.form.create.btn_reset')">{{
                    $t('dearbook.group.form.create.btn_reset') }}</SecondaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="groupForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">{{
                        $t('dearbook.group.form.create.btn_init_updated') }}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
