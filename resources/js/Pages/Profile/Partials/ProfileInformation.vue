<script setup>
defineProps({
    user: {
        type: Object,
    },
});
</script>

<template>
    <section>
        <div class="flex flex-col md:flex-row">
            <div class="w-full text-center md:text-left md:w-1/3 lg:max-w-36">
                <header>
                    <h2 class="font-semibold text-gray-400 lg:text-lg dark:text-gray-100">
                        {{ $t("Information") }}
                    </h2>
                </header>
            </div>

            <div
                class="pt-2 pl-2 mt-2 text-sm border-t-2 border-gray-400 md:pt-0 md:mt-0 md:border-t-0 md:border-l-2 lg:text-base md:pl-11">
                <div class="flex">
                    <div class="w-36 md:w-48">
                        <span class="underline">{{ $t("Name") }}</span>:
                    </div>

                    <div>{{ user.name }}</div>
                </div>

                <div class="flex">
                    <div class="w-36 md:w-48">
                        <span class="underline">{{ $t("Username") }}</span>:
                    </div>

                    <div>{{ user.username }}</div>
                </div>

                <div class="flex">
                    <div class="w-36 md:w-48">
                        <span class="underline">{{ $t("Email") }}</span>:
                    </div>

                    <div>{{ user.email }}</div>
                </div>
            </div>
        </div>
    </section>
</template>
