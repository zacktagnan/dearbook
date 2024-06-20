<script setup>
import { HandThumbUpIcon, } from "@heroicons/vue/24/outline";
import { ref, computed } from "vue";

import axiosClient from '@/axiosClient'

const showButtonsReactionBox = ref(false)

const props = defineProps({
    post: Object,
});

const emit = defineEmits(['callActiveShowNotificationToItem'])

// const sendReaction = () => {
const sendReaction = (from, type) => {
    axiosClient.post(route('post.reaction', props.post), {
        from_main_reaction_button: from,
        reaction_type: type,
        current_reaction_type: props.post.current_user_type_reaction,
    })
        .then(({ data }) => {
            props.post.current_user_type_reaction = data.current_user_type_reaction
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.total_of_reactions = data.total_of_reactions
        })
        .catch((error) => {
            // console.log('ERRORES: ', error.response.data.errors)
            emit('callActiveShowNotificationToItem', error.response.data.errors)
        })
    showButtonsReactionBox.value = false

    // setTypeReactionTextAndClasses()
}

const typeReactionTextAndClasses = computed(() => {
    if (!props.post.current_user_has_reaction) {
        return {
            text: 'Like',
            classes: ''
        }
    } else {
        return {
            text: switchTypeReactionTextAndClasses[props.post.current_user_type_reaction][0],
            classes: switchTypeReactionTextAndClasses[props.post.current_user_type_reaction][1]
        }
    }
})

// Siendo typeReactionTextAndClasses una referencia computada, no es necesario este método
// const setTypeReactionTextAndClasses = () => {
//     if (!props.post.current_user_has_reaction) {
//         typeReactionTextAndClasses.value = ''
//     } else {
//         typeReactionTextAndClasses.value = switchTypeReactionTextAndClasses[props.post.current_user_type_reaction]
//     }
// }
const switchTypeReactionTextAndClasses = {
    'like': ['Like', 'text-sky-500'],
    'love': ['Love', 'text-rose-500'],
    'care': '',
    'haha': '',
    'wow': '',
    'sad': '',
    'angry': '',
};
</script>

<template>
    <div class="relative w-1/2">
        <div class="z-[22] opacity-0 scale-0 absolute left-10 bottom-[38px] p-[3px] border rounded-full shadow flex items-center gap-1.5 bg-white h-12 transition-opacity duration-500 delay-500 ease-in-out"
            :class="{
                'opacity-100 scale-110': showButtonsReactionBox
            }" @mouseover="showButtonsReactionBox = true" @mouseleave="showButtonsReactionBox = false">
            <div class="tooltip tooltip-top" data-tip="Me gusta">
                <button @click="sendReaction(false, 'like')" class="flex justify-center w-10 group/like">
                    <img src="/img/emojis/like.png" alt="Like"
                        class="w-[35px] h-[35px] group-hover/like:w-[39px] group-hover/like:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me encanta">
                <button @click="sendReaction(false, 'love')" class="flex justify-center w-10 group/love">
                    <img src="/img/emojis/love.png" alt="Love"
                        class="w-[35px] h-[35px] group-hover/love:w-[39px] group-hover/love:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me importa">
                <button @click="sendReaction(false, 'care')" class="flex justify-center w-10 group/care">
                    <img src="/img/emojis/care.png" alt="Care"
                        class="w-[35px] h-[35px] group-hover/care:w-[39px] group-hover/care:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me divierte">
                <button @click="sendReaction(false, 'haha')" class="flex justify-center w-10 group/haha">
                    <img src="/img/emojis/haha.png" alt="Haha"
                        class="w-[35px] h-[35px] group-hover/haha:w-[39px] group-hover/haha:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me asombra">
                <button @click="sendReaction(false, 'wow')" class="flex justify-center w-10 group/wow">
                    <img src="/img/emojis/wow.png" alt="Wow"
                        class="w-[35px] h-[35px] group-hover/wow:w-[39px] group-hover/wow:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me entristece">
                <button @click="sendReaction(false, 'sad')" class="flex justify-center w-10 group/sad">
                    <img src="/img/emojis/sad.png" alt="Sad"
                        class="w-[35px] h-[35px] group-hover/sad:w-[39px] group-hover/sad:h-[39px] transition-all duration-150" />
                </button>
            </div>
            <div class="tooltip tooltip-top" data-tip="Me enoja">
                <button @click="sendReaction(false, 'angry')" class="flex justify-center w-10 group/angry">
                    <img src="/img/emojis/angry.png" alt="Angry"
                        class="w-[35px] h-[35px] group-hover/angry:w-[39px] group-hover/angry:h-[39px] transition-all duration-150" />
                </button>
            </div>
        </div>
        <button @click="sendReaction(true, 'like')" @mouseover="showButtonsReactionBox = true"
            @mouseleave="showButtonsReactionBox = false"
            class="flex items-center justify-center flex-1 w-full gap-1 px-4 py-2 rounded-lg hover:bg-gray-100"
            :class="typeReactionTextAndClasses.classes">
            <HandThumbUpIcon v-if="!post.current_user_has_reaction" class="w-6 h-6" />
            <img v-else-if="post.current_user_type_reaction == 'like'" src="/img/emojis/like.png" alt="Like"
                class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'love'" src="/img/emojis/love.png" alt="Love"
                class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'care'" src="/img/emojis/care.png" alt="Care"
                class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'haha'" src="/img/emojis/haha.png" alt="Haha"
                class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'wow'" src="/img/emojis/wow.png" alt="Wow" class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'sad'" src="/img/emojis/sad.png" alt="Sad" class="w-6" />
            <img v-else-if="post.current_user_type_reaction == 'angry'" src="/img/emojis/angry.png" alt="Angry"
                class="w-6" />
            {{ typeReactionTextAndClasses.text }}
        </button>
    </div>
</template>
