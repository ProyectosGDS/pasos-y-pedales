<script setup>
    const props = defineProps({
        text : {
            type : String,
            default : '',
            required : true
        },
        variant : {
            type : String,
            default : 'primary',
            validator : (val) => ['primary','dark','green','red','yellow','purple'].includes(val)
        }
    })
</script>
<template>
    <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-full"
        :class="{
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : props.variant == 'primary',
            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : props.variant == 'dark',
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : props.variant == 'green',
            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : props.variant == 'red',
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : props.variant == 'yellow',
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : props.variant == 'purple',
        }">

        {{ props.text }}
    </span>
</template>