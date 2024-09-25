<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const props = defineProps({
    group: {
        type: Object,
    },
})

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteGroup = () => {
    form.delete(route('group.destroy', props.group), {
        // Aplicando solo el preserveScroll cuando se devuelvan errores de validación
        preserveScroll: 'errors',
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset()
    form.errors = []
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{
                $t('dearbook.group.delete_option.header') }}</h2>

            <p class="mt-1 text-sm text-justify text-gray-600 dark:text-gray-400">
                {{ $t('dearbook.group.delete_option.intro') }}
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" :title="$t('dearbook.group.delete_option.header')">{{
            $t('dearbook.group.delete_option.header') }}
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $t('dearbook.group.delete_option.confirmation.question') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('dearbook.group.delete_option.confirmation.text') }}
                </p>

                <div class="mt-6">
                    <InputLabel for="password" :value="$t('Password')" class="sr-only" />

                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                        class="block w-3/4 mt-1" :placeholder="$t('Password')" @keyup.enter="deleteGroup" />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="flex justify-end mt-6">
                    <SecondaryButton @click="closeModal" :title="$t('Cancel')"> {{ $t('Cancel') }} </SecondaryButton>

                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteGroup" :title="$t('dearbook.group.delete_option.header')">
                        {{ $t('dearbook.group.delete_option.header') }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
