<script setup>
// import DeleteUserForm from "./Partials/DeleteUserForm.vue";
// import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
// import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";

defineProps({
    status: {
        type: String,
    },
});
</script>

<template>
    <div class="mt-2 space-y-6">
        <div class="px-4 bg-white shadow md:px-8 sm:py-4 dark:bg-gray-800 sm:rounded-lg">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $t("About") }}
            </h2>
        </div>
    </div>

    <div class="mt-4 space-y-6">
        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <!-- <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
                class="max-w-xl"
            /> -->
        </div>

        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <!-- <UpdatePasswordForm class="max-w-xl" /> -->
        </div>

        <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
            <!-- <DeleteUserForm class="max-w-xl" /> -->
        </div>
    </div>
</template>
