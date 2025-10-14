<script setup>

    const props = defineProps({
        url : {
            type : String,
            default : null,
        },
        defaultValue : {
            type : String,
            default : ''
        },
        shape : {
            type : String,
            default : 'round',
            validator : (val) => ['round','square'].includes(val.toLowerCase())
        }
    })

    defineOptions({ inheritAttrs: false })

</script>
<template>
    <img v-if="props.shape == 'round'" v-bind="$attrs" class="rounded-full object-cover object-center" :src="props.url ?? `https://ui-avatars.com/api/?name=${props.defaultValue}&color=7F9CF5&background=EBF4FF`" alt="Rounded avatar">
    <img v-else v-bind="$attrs" class="rounded-lg object-cover object-center" :src="props.url ?? `https://ui-avatars.com/api/?name=${props.defaultValue}&color=7F9CF5&background=EBF4FF`" alt="Default avatar">
</template>