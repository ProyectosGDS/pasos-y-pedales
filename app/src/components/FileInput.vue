<script setup>
    const props = defineProps({
        label : {
            type : String,
            defaul : ''
        },
        icon : {
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
        modelValue: null,
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
        <input type="file" :id="id" v-bind="$attrs" :value="modelValue" @input="handleInput" class="input w-full input-primary file:bg-gray-200 dark:file:bg-gray-800 file:px-2 file:py-1 pr-2 cursor-pointer" >   
    </div>
</template>