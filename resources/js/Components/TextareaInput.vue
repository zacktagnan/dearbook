<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    placeholder: String,
    autoResize: {
        type: Boolean,
        default: true,
    },
    classes: {
        type: String,
        default: 'border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600',
    },
    rows : {
        type: String,
        default: '2',
    }
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
    adjustHeight()
});

defineExpose({ focus: () => input.value.focus() });

const emit = defineEmits(['update:modelValue'])

const onInputChange = (event) => {
    emit('update:modelValue', event.target.value)
    adjustHeight()
}

const adjustHeight = () => {
    // Para que se vaya redimensionando con los saltos de línea...
    if (props.autoResize) {
        input.value.style.height = 'auto'
        input.value.style.height = input.value.scrollHeight + 'px'
    }
}
</script>

<template>
    <textarea
        :class="classes"
        :rows="rows"
        v-model="model"
        ref="input"
        :placeholder="placeholder"
        @input="onInputChange"></textarea>
</template>
