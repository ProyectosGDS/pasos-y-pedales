<script setup>
import Breadcrumb from '@/components/Breadcrumb.vue';
import Sidebar from '@/components/Sidebar.vue';

</script>

<template>
    <Sidebar />
    <div class="p-4 md:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            <Breadcrumb />
            <RouterView />
        </div>
    </div>
</template>