<script setup>
import OptionsDropDown from "@/Components/dearbook/OptionsDropDown.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    post: Object,
    index: Number,
});

const emit = defineEmits(['callCheckItem', 'callSubmitProcess'])

const authUser = usePage().props.auth.user;

const isPostAuthor = computed(
    () => authUser && authUser.id === props.post.user.id
);

const isTrashed = computed(
    () => props.post.deleted_at !== ''
);

const loadImage = (post) => {
    let image = ''
    if (post.attachments.length > 0) {
        image = post.attachments[0].path
    } else {
        image = post.user.avatar_path
    }

    if (image) {
        return 'storage/' + image
    }
    return null
}

const checkedId = defineModel()
</script>

<template>
    <div class="flex items-center justify-between py-2 gap-7" :class="index > 0 ? 'border-t border-slate-300' : ''">
        <div class="flex items-center">
            <div class="pl-1.5 pt-1 pb-1.5 pr-1.5 rounded-full group/check_all hover:bg-black/5">
                <input type="checkbox" :value="post.id" v-model="checkedId" @change="$emit('callCheckItem')"
                    class="w-[22px] h-[22px] rounded group-hover/check_all:bg-black/5">
            </div>
        </div>

        <div class="w-full p-2 rounded-lg hover:bg-black/5">
            <Link :href="route('post.show', { user: post.user.username, id: post.id })" title="Ver detalle"
                class="flex items-center w-full gap-2 p-1">
            <img :src="loadImage(post) || '/img/default_avatar.png'" class="w-[60px] h-[60px] border-2 rounded-full" />
            <div class="text-sm text-left">
                <div v-html="post.body || '(sin texto)'"></div>
                <small class="lowercase">{{ post.created_at_time }}</small>
            </div>
            </Link>
        </div>

        <div class="pr-2">
            <OptionsDropDown v-model="isPostAuthor" :is-trashed="isTrashed"
                @callRestoreItem="$emit('callSubmitProcess', 'restore', post.id)"
                @callForceDeleteItem="$emit('callSubmitProcess', 'force_delete', post.id)"
                :ellipsis-type-icon="'vertical'" :item-type="'post'" />
        </div>
    </div>
</template>
