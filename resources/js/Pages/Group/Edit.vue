<script setup>
import DeleteGroupForm from "./Partials/DeleteGroupForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
// import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";

defineProps({
    group: {
        type: Object,
    },
});

const emit = defineEmits(['callActiveShowNotification',])
</script>

<template>
    <div class="md:w-[55%] mx-auto my-6 space-y-6">
        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <UpdateProfileInformationForm @callActiveShowNotification="$emit('callActiveShowNotification')"
                :group="group" class="w-full" />
        </div>

        <!-- <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <UpdatePasswordForm class="max-w-xl" />
        </div> -->

        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <DeleteGroupForm :group="group" class="w-full" />
        </div>
    </div>
</template>
