<script setup>
    const props = defineProps({
        errors: {
            type: Array,
            default: () => [],
            required: true
        },
    })
</script>
<template>
    <div class="text-red-500  p-2 border-red-500 border-2 rounded-lg  my-4">
        <h1 class="text-lg font-medium">Ups! algo salio mal</h1>
        <ul class="list-disc ml-6 text-sm">
            <li v-for="error in props.errors">{{ error[0] }}</li>
        </ul>
    </div>
</template>