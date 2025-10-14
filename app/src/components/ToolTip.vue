<script setup>

const props = defineProps({
    message: String,
    position: {
        type: String,
        default: 'top',
        validator: val => ['top', 'right', 'bottom', 'left'].includes(val)
    }
})

defineOptions({
    inheritAttrs: false
})


</script>

<template>
    <label class="relative inline-flex cursor-pointer group">
        <span v-bind="$attrs"
            class="absolute tooltip"
            :class="{
                'bottom-full left-1/2 -translate-x-1/2 mb-1': props.position === 'top',
                'top-full left-1/2 -translate-x-1/2 mt-1': props.position === 'bottom',
                'text-left left-full top-1/2 -translate-y-1/2 ml-1': props.position === 'right',
                'text-right right-full top-1/2 -translate-y-1/2 mr-1': props.position === 'left'
            }">
            {{ message }}
        </span>
        <slot />
    </label>
</template>
