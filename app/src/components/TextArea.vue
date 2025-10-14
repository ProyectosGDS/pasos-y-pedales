<script setup>
    const props = defineProps({
        label : {
            type : String,
            defaul : ''
        },
        error : {
            type : Boolean,
            defaul : false,
            validator : (val) => {
                return [true,false].includes(val)
            }
        },
        modelValue: '',
    })

    defineOptions({ inheritAttrs: false })
    const emit = defineEmits(['update:modelValue'])

    const id = "input-" + Math.random().toString(36).substr(2, 9)

    const handleInput = (e) => {
        emit('update:modelValue', e.target.value)
    }


</script>

<template>
    <div>
        <label v-if="props.label" class="label-input">
            <span>{{ props.label ?? '' }}</span>
            <span v-if="$attrs.hasOwnProperty('required')" class="text-red-500">*</span>
        </label>
        <div class="flex items-center input" :class="props.error ? 'input-error' : 'input-primary'">
            <textarea :id="id" v-bind="$attrs" :value="modelValue" @input="handleInput" class=" py-2 px-4 outline-none w-full"></textarea>
        </div>
    </div>
</template>