<script setup>
import { computed } from "vue"
import Avatar from "./Avatar.vue"

const props = defineProps({
    text: { type: String, default: "" },
    size: { type: String, default: "md" , validator : (val) => ['lg','md','sm','xs'].includes(val) }, 
    icon: { type: String, default: null },
    iconRight: { type: String, default: null },
    img: { type: String, default: null },
    type: { type: String, default: "button" },
    disabled: { type: Boolean, default: false },
    class: { type: String, default: "" },
    loading: { type: Boolean, default: false },
})

const id = "btn-" + Math.random().toString(36).substr(2, 9)

const hasText = computed(() => !!props.text)
const hasIconOrImage = computed(
    () => !!props.icon || !!props.iconRight || !!props.img
)

const shapeClass = computed(() => {
    if (hasText.value && hasIconOrImage.value) {
        return "rounded-full"
    } else if (!hasText.value && hasIconOrImage.value) {
        return "rounded-full h-8 w-8 !p-0"
    } else {
        return "rounded-full"
    }
})

const sizeClass = computed(() => {
    switch (props.size) {
        case "lg":
            return "px-6 py-3 text-lg"
        case "md":
            return "px-5 py-2.5 text-base"
        case "sm":
            return "px-3 py-2 text-sm"
        default: // xs
            return "px-2 py-0.5 text-sm"
    }
})

const finalClass = computed(() =>
    `${props.class} btn ${shapeClass.value} ${sizeClass.value}`.trim()
)
</script>

<template>
    <button :id="id" :type="type" :class="['flex items-center justify-center', finalClass]" :disabled="disabled || loading">
        <!-- Normal content -->
        <span v-if="!loading" class="flex items-center gap-2">
            <!-- Imagen a la izquierda -->
            <Avatar v-if="img" :url="img" alt="btn-img" class="size-6"/>

            <!-- Icono izquierdo -->
            <Icon v-if="icon && !iconRight" :icon="icon" />

            <!-- Texto -->
            <span v-if="text" class="text-nowrap">{{ text }}</span>

            <!-- Icono derecho -->
            <Icon v-if="iconRight" :icon="iconRight" />
        </span>

        <!-- Loading content -->
        <span v-else class="inline-flex items-center gap-1">
            <Icon icon="fas fa-spinner" class="animate-spin" />
            <span v-if="text">Procesando</span>
        </span>
    </button>
</template>
