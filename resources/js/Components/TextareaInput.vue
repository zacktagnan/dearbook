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
    },
    blockItemClassName: String,
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
    adjustHeight()
});

const emit = defineEmits(['update:modelValue', 'callFocusIn', 'callFocusOut',])

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

const reInitAdjustHeight = () => {
    setTimeout(() => {
        adjustHeight()
    }, 10)
}

// const cancelEditingItemOnEscape = (e) => {
//     if (e.key === 'Escape') {
//         emit('callCancelEditingItem');
//     }
// };

const onFocusOut = (e) => {
    // console.log('EVENT', e)
    // console.log(e.target.tagName)
    // -------------------------------------------------------------------------------
    // if (e.target.tagName === 'TEXTAREA') {
    //     console.log('ES:', e.target.tagName)
    //     console.log('ES-tabindex:', e.target.tabIndex)
    // }
    // -------------------------------------------------------------------------------
    // console.log(props.blockItemClassName)
    if (e.relatedTarget && e.relatedTarget.tagName == 'BUTTON' && e.relatedTarget.className.includes(props.blockItemClassName)) {
        // console.log('CLICK en BUTTON')
        // console.log('ES-relatedTarget_tabindex:', e.relatedTarget.tabIndex)
        return
    } else if (e.relatedTarget === null || !e.target.className.includes(props.blockItemClassName)) {
        // console.log('CLICK en BUTTON')
        // console.log('ES-relatedTarget_difButton:', e.relatedTarget.tabIndex)
        emit('callFocusOut')
    }
    // -------------------------------------------------------------------------------
    // if (e.relatedTarget === null || (e.relatedTarget && e.relatedTarget.tagName !== 'BUTTON')) {
    // if (e.relatedTarget === null || e.target.className !== props.blockItemClassName) {
    //     // console.log('CLICK en BUTTON')
    //     // console.log('ES-relatedTarget_difButton:', e.relatedTarget.tabIndex)
    //     emit('callFocusOut')
    // }
}

// const onBlur = (e) => {
//     console.log('EVENT-Blur', e)
//     // emit('callFocusOut')
// }

defineExpose({
    focus: () => input.value.focus(),
    reInitAdjustHeight,
    input,
    // No funciona...
    // addEventListenerToCancelEditFromEsc: () => input.value.addEventListener('keydown', cancelEditingItemOnEscape),
    // removeEventListenerToCancelEditFromEsc: () => input.value.removeEventListener('keydown', cancelEditingItemOnEscape),
    // No funciona... Debido a que el sistema quiere interpretar el 2º párametro como si fuera un objeto
    // cuando se trata de un método como demanda el addEventListener/removeEventListener
    // addEventListenerToCancelEditFromEsc: (method) => input.value.addEventListener('keydown', method),
    // removeEventListenerToCancelEditFromEsc: (method) => input.value.removeEventListener('keydown', method),
});
</script>

<template>
    <textarea
        ref="input" v-model="model" :class="classes"
        :rows="rows" :placeholder="placeholder"
        @input="onInputChange" @focusin="$emit('callFocusIn')" @focusout="onFocusOut"></textarea>
</template>
