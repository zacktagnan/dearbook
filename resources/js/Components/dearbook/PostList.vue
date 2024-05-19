<script setup>
import PostItem from '@/Components/dearbook/PostItem.vue';

const post1 = {
    user: {
        id: 1,
        avatar: 'https://randomuser.me/api/portraits/men/0.jpg',
        name: 'Periko de los Palotes',
    },
    group: null,
    attachments: [
        {
            id: 1,
            name: 'test-1.png',
            url: 'https://picsum.photos/1000',
            mime: 'image/png',
        },
        {
            id: 2,
            name: 'test-2.png',
            url: 'https://picsum.photos/1000',
            mime: 'image/png',
        },
        {
            id: 3,
            name: 'mi-doc.docx',
            url: '#',
            mime: 'application/msword',
        },
    ],
    body: `
    <p>Incididunt mollit ex occaecat mollit. Commodo irure sit excepteur deserunt aliquip. Aliqua aliqua duis laboris officia minim officia dolore magna incididunt reprehenderit. Sit pariatur do pariatur adipisicing excepteur do et non excepteur deserunt elit non.</p>

    <p>Adipisicing dolor laboris adipisicing nostrud ex mollit irure mollit culpa sit qui laboris velit aliquip. Officia ea sunt occaecat tempor esse minim duis exercitation ipsum in dolore id est.</p>`,
    created_at: '2024-04-07 11:28',
}

const post2 = {
    user: {
        id: 2,
        avatar: 'https://randomuser.me/api/portraits/women/33.jpg',
        name: 'Halle Gría',
    },
    group: {
        id: 1,
        name: 'Laravel Fans',
    },
    body: `
    <p>Consequat tempor ea do deserunt veniam elit cillum anim velit.</p>

    <p>Adipisicing commodo aute velit magna ipsum voluptate. Voluptate id exercitation esse et.</p>`,
    created_at: '2023-12-15 09:55',
}
</script>

<template>
    <div>
        <PostItem :post="post1" />
        <PostItem :post="post2" />
        <PostItem :post="post2" />
        <PostItem :post="post2" />
    </div>
</template>
