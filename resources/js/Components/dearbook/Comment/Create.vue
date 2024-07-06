<script setup>
import { PaperAirplaneIcon, } from "@heroicons/vue/24/solid";
import TextareaInput from '@/Components/TextareaInput.vue';
import { ref, watch } from 'vue';

const commentTextAreaRef = ref(null);

const comment = ref('')

const emit = defineEmits([
    "callSendComment",
]);

const focusCommentTextArea = () => {
    if (commentTextAreaRef.value) {
        commentTextAreaRef.value.focus();
    }
};

const resetCommentTextArea = () => {
    if (commentTextAreaRef.value) {
        comment.value = '';
    }
};

watch(
    () => comment.value,
    () => {
        // console.log("COMMENT has changed...");
        disableSendCommentButton()
    }
)

const disableState = ref(true)
let disableStateTitleDefaultValue = 'Especificar un contenido'
const disableStateTitle = ref(disableStateTitleDefaultValue)
const disableSendCommentButton = () => {
    console.log('disableSendCommentButton...')
    disableState.value = comment.value.length <= 0

    disableStateTitle.value = disableState.value
        ? disableStateTitleDefaultValue
        : 'Comentar'
};

const reInitAdjustHeightTextArea = () => {
    if (commentTextAreaRef.value) {
        commentTextAreaRef.value.reInitAdjustHeight();
    }
};

defineExpose({
    focusCommentTextArea, resetCommentTextArea, reInitAdjustHeightTextArea,
})
</script>

<template>
    <div class="flex gap-2 mt-3">
        <a :href="route('profile.index', { username: $page.props.auth.user.username })"
            :title="'Perfil de ' + $page.props.auth.user.name">
            <img :src="$page.props.auth.user.avatar_url ||
            '/img/default_avatar.png'" class="w-8 transition-all border-2 rounded-full hover:border-cyan-500"
                :alt="$page.props.auth.user.name" />
        </a>

        <div class="flex flex-col w-full gap-0">
            <TextareaInput ref="commentTextAreaRef" v-model="comment" :placeholder="'Comentar como ' + $page.props.auth.user.name" :class="'w-full rounded-none rounded-t-lg border-0 bg-gray-200/50 text-[15px] ring-0 focus:ring-0 resize-none pt-2 max-h-28'" rows="1" />

            <div class="flex justify-end px-3 pb-2 rounded-none rounded-b-lg bg-gray-200/50">
                <button
                    @click="$emit('callSendComment', comment)"
                    :disabled="disableState" :title="disableStateTitle" class="text-cyan-700 hover:text-cyan-500 disabled:text-gray-400 disabled:cursor-not-allowed">
                    <PaperAirplaneIcon class="w-[18px] h-[18px]" />
                </button>
            </div>
        </div>
    </div>
</template>
