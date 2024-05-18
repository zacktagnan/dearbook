<script setup>
import GroupItem from '@/Components/dearbook/GroupItem.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const searchKeyword = ref('')
</script>

<template>
    <div class="px-3">
        <h2 class="text-xl font-bold">Grupos</h2>

        <TextInput class="mt-2" :model="searchKeyword" placeholder="Teclea el grupo a buscar" />

        <div class="py-2">
            <div v-if="false" class="flex text-gray-400">
                <p class="w-full text-center">
                    No estás unido a ningún grupo
                    <br>
                    (You are not joined to any group)
                </p>
            </div>

            <div v-else>
                <GroupItem
                    image="https://picsum.photos/100" title="Laravel Fans"
                    description="Lorem ipsum dolor sit amet consectetur adipisicing elit."
                    :class="{'mt-1': false}"
                />
                <GroupItem
                    image="https://picsum.photos/100" title="Backenderos"
                    description="Reprehenderit, inventore excepturi aut, explicabo unde, id et accusantium dolor."
                    :class="{'mt-1': true}"
                />
                <GroupItem
                    image="https://picsum.photos/100" title="Donostiarras"
                    description="Tempore deserunt suscipit ratione numquam dolores maiores."
                    :class="{'mt-1': true}"
                />
            </div>
        </div>
    </div>
</template>
