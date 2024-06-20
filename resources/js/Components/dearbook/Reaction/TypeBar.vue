<script setup>
const props = defineProps({
    showReactionTypeBar: Boolean,
});

const emit = defineEmits(['callChangeShowReactionTypeBar', 'callSendReaction'])
</script>

<template>
    <div class="z-[22] opacity-0 scale-0 absolute left-10 bottom-[38px] p-[3px] border rounded-full shadow flex items-center gap-1.5 bg-white h-12 transition-opacity duration-500 delay-500 ease-in-out"
        :class="{
            'opacity-100 scale-110': showReactionTypeBar
        }" @mouseover="$emit('callChangeShowReactionTypeBar', true)" @mouseleave="$emit('callChangeShowReactionTypeBar', false)">
        <div class="tooltip tooltip-top" data-tip="Me gusta">
            <button @click="$emit('callSendReaction', false, 'like')" class="flex justify-center w-10 group/like">
                <img src="/img/emojis/like.png" alt="Like"
                    class="w-[35px] h-[35px] group-hover/like:w-[39px] group-hover/like:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me encanta">
            <button @click="$emit('callSendReaction', false, 'love')" class="flex justify-center w-10 group/love">
                <img src="/img/emojis/love.png" alt="Love"
                    class="w-[35px] h-[35px] group-hover/love:w-[39px] group-hover/love:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me importa">
            <button @click="$emit('callSendReaction', false, 'care')" class="flex justify-center w-10 group/care">
                <img src="/img/emojis/care.png" alt="Care"
                    class="w-[35px] h-[35px] group-hover/care:w-[39px] group-hover/care:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me divierte">
            <button @click="$emit('callSendReaction', false, 'haha')" class="flex justify-center w-10 group/haha">
                <img src="/img/emojis/haha.png" alt="Haha"
                    class="w-[35px] h-[35px] group-hover/haha:w-[39px] group-hover/haha:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me asombra">
            <button @click="$emit('callSendReaction', false, 'wow')" class="flex justify-center w-10 group/wow">
                <img src="/img/emojis/wow.png" alt="Wow"
                    class="w-[35px] h-[35px] group-hover/wow:w-[39px] group-hover/wow:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me entristece">
            <button @click="$emit('callSendReaction', false, 'sad')" class="flex justify-center w-10 group/sad">
                <img src="/img/emojis/sad.png" alt="Sad"
                    class="w-[35px] h-[35px] group-hover/sad:w-[39px] group-hover/sad:h-[39px] transition-all duration-150" />
            </button>
        </div>
        <div class="tooltip tooltip-top" data-tip="Me enoja">
            <button @click="$emit('callSendReaction', false, 'angry')" class="flex justify-center w-10 group/angry">
                <img src="/img/emojis/angry.png" alt="Angry"
                    class="w-[35px] h-[35px] group-hover/angry:w-[39px] group-hover/angry:h-[39px] transition-all duration-150" />
            </button>
        </div>
    </div>
</template>
