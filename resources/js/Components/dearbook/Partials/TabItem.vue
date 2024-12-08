<script setup>
defineProps({
    selected: Boolean,
    text: {
        type: String,
        default: '',
    },
    textColor: {
        type: String,
        default: 'text-sky-500',
    },
    reactionType: {
        type: String,
        default: "",
    },
    showTypeIcon: {
        type: Boolean,
        default: true,
    },
    totalOfReactions: Number,
});
</script>

<template>
    <div :class="[
        'border-b-2 focus:outline-none',
        selected
            ? 'border-cyan-500'
            : 'border-white hover:border-gray-500',
    ]">
        <button
            :class="[
                'px-1 md:px-2 py-2.5 outline-none font-bold text-nowrap border-0',
                selected
                    ? 'bg-white dark:bg-slate-800 ' + textColor
                    : 'text-gray-400 hover:text-gray-500',
            ]"
        >
            <div v-if="showTypeIcon" class="flex items-center gap-1">
                <img
                    :src="'/img/emojis/' + reactionType + '.png'"
                    :alt="text"
                    class="w-5 h-5"
                /> {{ totalOfReactions }}
            </div>
            <span
                v-else
            >
                {{ text }} {{ totalOfReactions }}
            </span>
        </button>
    </div>
</template>
