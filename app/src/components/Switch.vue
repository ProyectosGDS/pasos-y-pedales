<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        required: true
    },
    values: {
        type: Array,
        default: () => [false, true],
        validator: (value) => value.length === 2
    },
    error: {
        type: Boolean,
        default: false
    },
    primaryColor: {
        type: String,
        default: 'bg-blue-500'
    },
    secondaryColor: {
        type: String,
        default: 'bg-gray-500'
    }
})

const emit = defineEmits(['update:modelValue'])

const isValidValue = computed(() => props.values.includes(props.modelValue))

const isActive = computed(() => props.modelValue === props.values[0])

const isNeutral = computed(() => !isValidValue.value)

const toggleChecked = () => {
    if (isNeutral.value) {
        emit('update:modelValue', props.values[0])
    } else {
        const newValue = isActive.value ? props.values[1] : props.values[0]
        emit('update:modelValue', newValue)
    }
}
</script>

<template>
    <label class="relative cursor-pointer block aspect-[1.6/0.75] rounded-full transition-all duration-500" 
            :class="{
                'bg-red-500': error,
                [primaryColor]: isValidValue && isActive,
                [secondaryColor]: isValidValue && !isActive,
                'bg-gray-400': isNeutral
            }">
        <input class="peer/input hidden" type="checkbox" :checked="isActive" @change="toggleChecked">
        <div class="absolute top-1/2 aspect-square h-[70%] -translate-y-1/2 bg-white rounded-full transition-all duration-500"
            :class="{
                'right-[6%]': isActive,
                'right-[57%]': isValidValue && !isActive,
                'right-[31.5%]': isNeutral,
                'bg-gray-200': isNeutral
            }">
        </div>
    </label>
</template>