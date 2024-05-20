<script setup>

defineProps({
    user: {
        type: Object,
    },
});
</script>

<template>
    <section>
        <div class="flex">
            <div class="w-1/5">
                <header>
                    <h2 class="text-lg font-semibold text-gray-400 dark:text-gray-100">{{ $t('Profile Information') }}</h2>
                </header>
            </div>

            <div class="text-base border-l-2 border-gray-400 pl-11 ml-11">
                <div class="flex">
                    <div class="w-48 underline">{{ $t('Name') }}:</div>

                    <div>{{ user.name }}</div>
                </div>

                <div class="flex">
                    <div class="w-48 underline">{{ $t('Username') }}:</div>

                    <div>{{ user.username }}</div>
                </div>

                <div class="flex">
                    <div class="w-48 underline">{{ $t('Email') }}:</div>

                    <div>{{ user.email }}</div>
                </div>
            </div>
        </div>
    </section>
</template>
