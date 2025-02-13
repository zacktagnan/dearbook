<script setup>
import PostItem from '@/Components/dearbook/Post/Item.vue';
import PostModal from "@/Components/dearbook/Post/Modal.vue";
import PostDetailModal from "@/Components/dearbook/Post/ModalForDetail.vue";
import AttachmentModal from "@/Components/dearbook/Attachment/Modal.vue";
import UserReactionsModal from '@/Components/dearbook/Reaction/Modal.vue'
import ConfirmPostDeletionModal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUpdated, watch } from 'vue';

const props = defineProps({
    // En Home, posts llega como Object por ser una consulta paginada, aquí es recibido como Array pues lo que llega es, realmente, no los posts sino posts.data
    'posts': Array,
    after_comment_deleted: {
        type: Object,
    },
    parent_page_name: {
        type: String,
        default: '',
    },
})

const showEditModal = ref(false)
const showDetailModal = ref(false)
const showAttachmentsModal = ref(false)
const showReactionsModal = ref(false)
const tabIndexReactionsModal = ref(0)

const postToEdit = ref({})
const postDetail = ref({})
const entityWithAttachmentsToPreview = ref({})
const entityWithReactions = ref({})

const openEditModal = (post) => {
    postToEdit.value = post
    showEditModal.value = true
}

const openDetailModal = (post) => {
    postDetail.value = post
    showDetailModal.value = true
}

const openAttachmentsModal = (entity, index, entityPrefix) => {
    entityWithAttachmentsToPreview.value = {
        entity,
        index,
        entityPrefix,
    }
    showAttachmentsModal.value = true
}

const openUserReactionsModal = (entity, tabIndex) => {
    // entityWithReactions.value = entity
    entityWithReactions.value = {
        'current_user_has_reaction': entity.current_user_has_reaction,
        'current_user_type_reaction': entity.current_user_type_reaction,
        'all': entity.all_user_reactions,
        'like': entity.like_user_reactions,
        'love': entity.love_user_reactions,
        'care': entity.care_user_reactions,
        'haha': entity.haha_user_reactions,
        'wow': entity.wow_user_reactions,
        'sad': entity.sad_user_reactions,
        'angry': entity.angry_user_reactions,
    }
    showReactionsModal.value = true
    tabIndexReactionsModal.value = tabIndex
}

const postDetailModalRef = ref(null)
const entityToDelete = ref({})
const showingConfirmDeletion = ref(false);
const showConfirmDeletion = (entity, entityPrefix) => {
    let to = 'none'
    if (entityPrefix !== 'post.comment') {
        if (entity.deleted_at === '' && entity.archived_at === '') {
            if (entity.group) {
                to = 'group_' + entity.group.slug
            } else {
                to = 'home'
            }
        } else if (entity.archived_at !== '') {
            to = 'archive'
        }
    }
    entityToDelete.value = {
        entity,
        entityPrefix,
        to,
    }
    showingConfirmDeletion.value = true;
};
const closeConfirmDeletion = () => {
    showingConfirmDeletion.value = false;
};

const postItemRef = ref({})
const deleteEntity = () => {
    // Se ha hecho necesario pasar el ID de la entidad en vez de toda la entidad en ambos casos
    if (entityToDelete.value.entityPrefix === 'post.comment' || entityToDelete.value.entityPrefix === 'post') {
        entityToDelete.value.entity = entityToDelete.value.entity.id
    }
    router.delete(route(entityToDelete.value.entityPrefix + ".destroy", {
        entity: entityToDelete.value.entity,
        to: entityToDelete.value.to,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            closeConfirmDeletion()

            if (entityToDelete.value.entityPrefix === 'post.comment') {
                postItemRef.value[props.after_comment_deleted.post_id].restartGeneralDataFromPostComments(props.after_comment_deleted.general_data)
                postItemRef.value[props.after_comment_deleted.post_id].restartPostCommentList(props.after_comment_deleted.latest_comments, props.after_comment_deleted.all_comments)
            }

            // El aplicar este RELOAD puede que no tenga efecto alguno y, sobre todo, tras establecer el WATCH sobre props.posts
            if (entityToDelete.value.entityPrefix === 'post') {
                router.reload()
            }

            if (showDetailModal.value === true) {
                // if (entityToDelete.value.entityPrefix === 'post') {
                //     showDetailModal.value = false
                // } else if (entityToDelete.value.entityPrefix === 'post.comment') {
                //     postDetailModalRef.value.filterDeletedComment(entityToDelete.value.entity)
                // }
                if (entityToDelete.value.entityPrefix === 'post.comment') {
                    console.log('Ejecutando...filterDeletedComment ¿? ... desde Post/List - commentID: [' + entityToDelete.value.entity + ']')
                    postDetailModalRef.value.filterDeletedComment(entityToDelete.value.entity)
                }
                // Solo para ¿POST?
                // showDetailModal.value = false

                if (entityToDelete.value.entityPrefix === 'post') {
                    showDetailModal.value = false
                }
            }
        },
    });
};

const archiveItem = (postId) => {
    let from = 'home'

    router.get(route('post.archive', {
        id: postId,
        from,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('OK-Archivation')
        },
        onError: (errors) => {
            console.log('ERRORES-Archivation', errors);
        },
    })
}

// -------------------------------------

import NotificationBox from "@/Components/dearbook/NotificationBox.vue";

const errorsFromPost = ref({})

const showNotification = ref(true)
const notificationBoxRef = ref(null)
const notificationMsg = ref(null)

const timer = ref(null)
const timeOnSeconds = ref(0)
const maxTimeOnSecondsForNotificationBox = usePage().props.maxTimeOnSecondsForNotificationBox;

const activeShowGeneralNotification = (msg) => {
    notificationMsg.value = msg
    showNotification.value = true

    // setTimeout(() => {
    //     closingNotification('notification')
    // }, 3000)
    timer.value = null
    timeOnSeconds.value = 0
    startClosingNotification()
}

const activeShowNotification = (errors) => {
    errorsFromPost.value = errors
    showNotification.value = true

    // setTimeout(() => {
    //     closingNotification('notification')
    // }, 3000)
    timer.value = null
    timeOnSeconds.value = 0
    startClosingNotification()
}

const startClosingNotification = () => {
    timer.value = setInterval(() => {
        // if (notificationBoxRef.value) {
        //     timeOnSeconds.value++
        //     console.log('timeOnSeconds', timeOnSeconds.value)

        //     if (timeOnSeconds.value >= maxTimeOnSecondsForNotificationBox) {
        //         stopClosingNotification()
        //         closingNotification('notification')
        //         console.log('timeOnSeconds tras CLOSING', timeOnSeconds.value)
        //     }
        // }
        if (notificationBoxRef.value && timeOnSeconds.value < maxTimeOnSecondsForNotificationBox) {
            timeOnSeconds.value++
        } else {
            stopClosingNotification()
            closingNotification('notification')
        }
    }, 1000)
}

const stopClosingNotification = () => {
    clearInterval(timer.value)
}

const closingNotification = (className) => {
    // Siempre que no se haya cerrado ya, manualmente, la notificación
    // (o sea, que exista, es decir, que aún no sea NULL)
    if (notificationBoxRef.value) {
        notificationBoxRef.value.fadeOutEffect(className)
    }
}

const closeShowNotification = () => {
    showNotification.value = false
}

watch(
    () => props.posts,
    () => {
        // console.log('POSTs listado ha cambiado...')
        reinitAllPosts()

        // Revisando cada Post de la lista para ver si tiene que ser marcado como Fijado o no
        // cada vez que cambia el conjunto de "props.posts"
        allPosts.value.data.forEach((post) => {
            const postItem = postItemRef.value[post.id]
            if (postItem && postItem.updatePinnedState) {
                postItem.updatePinnedState()
            }
        })
    }
)


import axiosClient from '@/axiosClient'

const page = usePage()

const allPosts = ref({
    data: page.props.posts.data,
    next: page.props.posts.links.next,
})

const reinitAllPosts = () => {
    allPosts.value = {
        data: page.props.posts.data,
        next: page.props.posts.links.next,
    }
}

// onUpdated(() => {
//     allPosts.value = {
//         data: page.props.posts.data,
//         next: page.props.posts.links.next,
//     }
// })

const loadMoreIntersectRef = ref(null)

const loadMore = () => {
    // console.log('Loading more Posts...')

    if (!allPosts.value.next) {
        return
    }

    axiosClient.get(allPosts.value.next)
        // .then(res => {
        //     debugger
        //     allPosts.value.data = [...allPosts.value.data, ...res.data]
        //     allPosts.value.next = res.links.next
        // })
        .then(({ data }) => {
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
        rootMargin: '-250px 0px 0px 0px'
    })

    observer.observe(loadMoreIntersectRef.value)
})
</script>

<template>
    <div>
        <PostItem v-for="post in allPosts.data" :ref="el => postItemRef[post.id] = el" :post="post" :parent_page_name="parent_page_name"
            @callOpenEditModal="openEditModal" @callArchiveItem="archiveItem" @callOpenDetailModal="openDetailModal"
            @callOpenAttachmentsModal="openAttachmentsModal" @callOpenUserReactionsModal="openUserReactionsModal"
            @callConfirmDeletion="showConfirmDeletion" @callActiveShowNotification="activeShowNotification"
            @callActiveShowGeneralNotification="activeShowGeneralNotification" />

        <div ref="loadMoreIntersectRef" class="-translate-y-32"></div>

        <PostModal :post="postToEdit" v-model="showEditModal" @callActiveShowNotification="activeShowNotification" />

        <PostDetailModal ref="postDetailModalRef" :post="postDetail" v-model="showDetailModal"
            @callOpenEditModal="openEditModal" @callArchiveItem="archiveItem"
            @callOpenAttachmentsModal="openAttachmentsModal" @callOpenUserReactionsModal="openUserReactionsModal"
            @callConfirmDeletion="showConfirmDeletion" @callActiveShowNotification="activeShowNotification" />

        <AttachmentModal :attachments="entityWithAttachmentsToPreview.entity?.attachments || []"
            :entity-prefix="entityWithAttachmentsToPreview.entityPrefix"
            v-model:index="entityWithAttachmentsToPreview.index" v-model="showAttachmentsModal" />

        <UserReactionsModal v-model="showReactionsModal" :entity="entityWithReactions"
            :default-index="tabIndexReactionsModal" />

        <ConfirmPostDeletionModal :show="showingConfirmDeletion" @close="closeConfirmDeletion">
            <div class="p-6">
                <template v-if="entityToDelete.entityPrefix === 'post'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $t('dearbook.post.confirm.deletion.question') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t('dearbook.post.confirm.deletion.message') }}
                    </p>
                </template>
                <template v-else-if="entityToDelete.entityPrefix === 'post.comment'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $t('dearbook.comment.confirm.deletion.question') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t('dearbook.comment.confirm.deletion.message') }}
                    </p>
                </template>

                <div class="flex justify-end mt-6">
                    <SecondaryButton @click="closeConfirmDeletion" :title="$t('Cancel')"> {{ $t('Cancel') }}
                    </SecondaryButton>

                    <DangerButton v-if="entityToDelete.entityPrefix === 'post'" class="ms-3" @click="deleteEntity"
                        :title="$t('dearbook.post.confirm.deletion.button_text')">
                        {{ $t('dearbook.post.confirm.deletion.button_text') }}
                    </DangerButton>
                    <DangerButton v-else-if="entityToDelete.entityPrefix === 'post.comment'" class="ms-3"
                        @click="deleteEntity" :title="$t('dearbook.comment.confirm.deletion.button_text')">
                        {{ $t('dearbook.comment.confirm.deletion.button_text') }}
                    </DangerButton>
                </div>
            </div>
        </ConfirmPostDeletionModal>

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-if="showNotification && errorsFromPost.body" :title="'Error'" :message="errorsFromPost.body" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.attachments" :title="'Error'"
            :message="errorsFromPost.attachments" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.reaction_type" :title="'Error'"
            :message="errorsFromPost.reaction_type" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-else-if="showNotification && errorsFromPost.comment" :title="'Error'" :message="errorsFromPost.comment" />

        <NotificationBox ref="notificationBoxRef" @callCloseShowNotification="closeShowNotification"
            @callOnMouseOver="stopClosingNotification" @callOnMouseLeave="startClosingNotification"
            v-show="showNotification && notificationMsg" :title="'Info'" :message="notificationMsg" />
    </div>
</template>
