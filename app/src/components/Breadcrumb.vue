<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router'
const route = useRoute()

const breadcrumb = computed(() => {
    return route.fullPath.split("/");
})

</script>
<template>
    <nav class="flex mb-3">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li v-for="(item,key) in breadcrumb">
                <div class="flex items-center">
                    <Icon :icon="key == 0 ? 'house' :'chevron-right'"/>
                    <a href="#" class="ms-1 font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
                        {{ key == 0 ? 'Home' : item.charAt(0).toUpperCase() + item.slice(1) }}
                    </a>
                </div>
            </li>
        </ol>
    </nav>
</template>