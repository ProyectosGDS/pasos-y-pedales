<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useFloating, offset, flip, shift, autoUpdate } from '@floating-ui/vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    modelValue: {
        type: [Object, Array, String, Number, null],
        default: (props) => (props.multiple ? [] : null),
    },
    label: {
        type: String,
        default: ''
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Seleccione una opción',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    // Nueva prop: controla si devuelve el objeto completo o solo el value
    returnType: {
        type: String,
        default: 'object', // 'object' | 'value'
        validator: (value) => ['object', 'value'].includes(value)
    },
    // Configuración de virtual scrolling
    virtualScroll: {
        type: Boolean,
        default: false
    },
    virtualScrollItemSize: {
        type: Number,
        default: 40 // altura aproximada de cada item en px
    },
    virtualScrollVisibleItems: {
        type: Number,
        default: 10 // número de items visibles
    },
    searching : {
        type : Boolean,
        default : true
    },
    icon : {
        type : String,
        default : null,
    },
    error : {
        type : Boolean,
        default : false
    }
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchText = ref('')

const reference = ref(null)
const floating = ref(null)

// Variables para virtual scrolling
const scrollTop = ref(0)
const containerHeight = ref(0)

const { floatingStyles } = useFloating(reference, floating, {
    placement: 'bottom-start',
    middleware: [offset(8), flip(), shift()],
    whileElementsMounted: autoUpdate,
})

onClickOutside(floating, (event) => {
    if (reference.value && reference.value.contains(event.target)) {
        return
    }
    closeDropdown()
}, { ignore: [reference] })

const isDisabled = computed(() => props.disabled || props.loading)
const isMultiple = computed(() => props.multiple)

const currentSelection = computed(() => {
    return props.modelValue
})

const hasSelection = computed(() => {
    if (isMultiple.value) {
        return Array.isArray(currentSelection.value) && currentSelection.value.length > 0
    }

    return currentSelection.value !== null && currentSelection.value !== undefined
})

const filteredOptions = computed(() => {
    if (!searchText.value) {
        return props.options
    }
    const search = searchText.value.toLowerCase()
    return props.options.filter((option) =>
        option.label.toLowerCase().includes(search)
    )
})

// Computed properties para virtual scrolling
const totalHeight = computed(() => {
    return filteredOptions.value.length * props.virtualScrollItemSize
})

const visibleOptions = computed(() => {
    if (!props.virtualScroll) {
        return filteredOptions.value
    }

    const startIndex = Math.floor(scrollTop.value / props.virtualScrollItemSize)
    const endIndex = Math.min(
        startIndex + props.virtualScrollVisibleItems,
        filteredOptions.value.length
    )

    return filteredOptions.value.slice(startIndex, endIndex).map((option, index) => ({
        ...option,
        _virtualIndex: startIndex + index,
        _transform: `translateY(${(startIndex + index) * props.virtualScrollItemSize}px)`
    }))
})

const visibleRange = computed(() => {
    if (!props.virtualScroll) return { start: 0, end: filteredOptions.value.length }

    const startIndex = Math.floor(scrollTop.value / props.virtualScrollItemSize)
    return {
        start: startIndex,
        end: Math.min(startIndex + props.virtualScrollVisibleItems, filteredOptions.value.length)
    }
})

/**
 * Abre o cierra el dropdown.
 */
function toggleDropdown() {
    if (isDisabled.value) return
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        searchText.value = ''
        nextTick(() => {
            if (floating.value && props.virtualScroll) {
                containerHeight.value = floating.value.clientHeight
            }
        })
    }
}

/**
 * Cierra el dropdown.
 */
function closeDropdown() {
    isOpen.value = false
}

/**
 * Verifica si una opción está seleccionada.
 */
function isSelected(option) {
    if (isMultiple.value && Array.isArray(currentSelection.value)) {
        if (props.returnType === 'value') {
            return currentSelection.value.some(item => item === option.value)
        }
        return currentSelection.value.some((item) => item.value === option.value)
    }

    if (props.returnType === 'value') {
        return currentSelection.value === option.value
    }
    return currentSelection.value && currentSelection.value.value === option.value
}

/**
 * Obtiene el valor a devolver según returnType
 */
function getReturnValue(option) {
    if (props.returnType === 'value') {
        return option.value
    }
    return option
}

/**
 * Obtiene el array de valores a devolver según returnType
 */
function getReturnArray(currentArray) {
    if (props.returnType === 'value') {
        return currentArray.map(option => option.value)
    }
    return currentArray
}

/**
 * Lógica de selección de opción.
 */
function selectOption(option) {
    if (option.disabled) return

    // Si la opción tiene una acción personalizada
    if (option.action) {
        if (typeof option.action === 'function') {
            option.action()
        }
        if (!isMultiple.value) {
            closeDropdown()
        }
        return
    }

    // Lógica de selección normal
    if (isMultiple.value) {
        const currentArray = Array.isArray(currentSelection.value) ?
            (props.returnType === 'value' ?
                props.options.filter(opt => currentSelection.value.includes(opt.value)) :
                currentSelection.value.slice()
            ) : []

        const index = currentArray.findIndex((item) =>
            props.returnType === 'value' ?
                item.value === option.value :
                item.value === option.value
        )

        if (index > -1) {
            // Remover si ya está seleccionado
            currentArray.splice(index, 1)
        } else {
            // Agregar si no está seleccionado
            currentArray.push(option)
        }
        updateModel(getReturnArray(currentArray))
    } else {
        // Selección simple
        const isCurrentlySelected = props.returnType === 'value' ?
            currentSelection.value === option.value :
            (currentSelection.value && currentSelection.value.value === option.value)

        const newValue = isCurrentlySelected ? null : getReturnValue(option)
        updateModel(newValue)
        closeDropdown()
    }
}

/**
 * Remueve una opción en multiselección (desde el tag de la selección).
 */
function removeOption(option) {
    if (isMultiple.value && Array.isArray(currentSelection.value)) {
        let currentArray

        if (props.returnType === 'value') {
            // Convertir valores a objetos para poder filtrar
            currentArray = props.options.filter(opt =>
                currentSelection.value.includes(opt.value) && opt.value !== option.value
            )
        } else {
            currentArray = currentSelection.value.filter(
                (item) => item.value !== option.value
            )
        }

        updateModel(getReturnArray(currentArray))
    }
}

/**
 * Maneja el scroll para virtual scrolling
 */
function handleScroll(event) {
    if (!props.virtualScroll) return
    scrollTop.value = event.target.scrollTop
}

/**
 * Emite el evento de actualización del v-model.
 */
function updateModel(newValue) {
    emit('update:modelValue', newValue)
    emit('change', newValue)
}

/**
 * Obtiene la etiqueta para mostrar basado en el valor
 */
function getDisplayLabel(value) {
    if (props.returnType === 'value') {
        const option = props.options.find(opt => opt.value === value)
        return option ? option.label : value
    }
    return value ? value.label : ''
}

watch(isDisabled, (newVal) => {
    if (newVal) {
        closeDropdown()
    }
})

// Resetear scroll cuando se filtran opciones
watch(filteredOptions, () => {
    if (props.virtualScroll) {
        scrollTop.value = 0
    }
})
</script>

<template>
    <div>
        <label v-if="props.label" class="label-input">
            <span>{{ props.label ?? '' }}</span>
            <span v-if="$attrs.hasOwnProperty('required')" class="text-red-500">*</span>
        </label>
        <div class="relative inline-block w-full">
            <button 
                ref="reference" 
                type="button" 
                :disabled="isDisabled" 
                :class="[
                    'flex w-full items-center justify-between p-2 input transition-all',
                    props.error ? 'input-error' : 'input-primary',
                    isDisabled ? 'cursor-not-allowed bg-gray-100 opacity-70' : 'cursor-pointer',
                    { 'border-blue-500 ring-1 ring-blue-500': isOpen },
                ]" 
                @click="toggleDropdown" >
                <div class="flex items-center gap-2">
                    <Icon :icon="props.icon" class="text-lg" />
                    <span v-if="!hasSelection">
                        {{ placeholder }}
                    </span>
    
                    <div v-else class="flex flex-wrap items-center gap-2">
                        <template v-if="isMultiple">
                            <span v-for="item in currentSelection" :key="props.returnType === 'value' ? item : item.value"
                                class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                {{ getDisplayLabel(item) }}
                                <button type="button" class="ml-1 text-blue-700 hover:text-blue-900"
                                    @click.stop="removeOption(props.returnType === 'value' ? { value: item } : item)">
                                    &times;
                                </button>
                            </span>
                        </template>
                        <template v-else>
                            <span>{{ getDisplayLabel(currentSelection) }}</span>
                        </template>
                    </div>
                </div>

                <Icon v-if="loading" icon="spinner" class="animate-spin" />
                <Icon v-else icon="chevron-down" class="transition-transform" :class="{ 'rotate-180': isOpen }" />

            </button>

            <div v-if="isOpen" ref="floating" :style="floatingStyles"
                class="z-50 w-full overflow-hidden rounded-md border-2 border-gray-600 bg-gray-50 dark:bg-gray-800 shadow-lg">
                <div class="p-2">
                    <Input v-if="props.searching" 
                        icon="search" 
                        v-model="searchText" 
                        type="search" 
                        placeholder="Buscar opción..."
                        @keydown.stop 
                    />
                </div>

                <div v-if="loading && !filteredOptions.length"
                    class="flex items-center justify-center p-4 text-sm text-blue-500">
                    <Icon icon="spinner" class="animate-spin" />
                    Cargando opciones...
                </div>

                <div v-else :class="[
                    'overflow-y-auto',
                    props.virtualScroll ? 'relative' : ''
                ]"
                    :style="props.virtualScroll ? { height: `${props.virtualScrollVisibleItems * props.virtualScrollItemSize}px` } : { maxHeight: '240px' }"
                    @scroll="handleScroll">
                    <div v-if="props.virtualScroll" class="absolute w-full" :style="{ height: `${totalHeight}px` }">
                        <ul class="absolute w-full">
                            <li v-for="option in visibleOptions" :key="option.value" :style="option._transform" :class="[
                                'absolute left-0 right-0 flex cursor-pointer items-center justify-between p-2 text-sm transition-colors',
                                option.disabled
                                    ? 'cursor-not-allowed bg-gray-700 dark:text-gray-900'
                                    : 'hover:bg-gray-900 active:bg-blue-100',
                                isSelected(option) ? 'bg-gray-900 font-medium text-blue-800' : 'dark:text-gray-50',
                            ]" @click="selectOption(option)">
                                <div class="flex items-center gap-3">
                                    <img v-if="option.img" :src="option.img" alt="" class="h-6 w-6 rounded-full object-cover" />
                                    <div class="flex flex-col">
                                        <span>{{ option.label }}</span>
                                        <span v-if="option.preview" class="text-xs text-gray-400">
                                            {{ option.preview }}
                                        </span>
                                    </div>
                                </div>

                                <svg v-if="isMultiple && isSelected(option)" class="h-4 w-4 text-blue-600"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                        clip-rule="evenodd" />
                                </svg>
                            </li>
                        </ul>
                    </div>

                    <!-- Lista normal (sin virtual scrolling) -->
                    <ul v-else class="p-1">
                        <li v-for="option in filteredOptions" :key="option.value" :class="[
                            'flex cursor-pointer items-center justify-between rounded-md p-2 text-sm transition-colors',
                            option.disabled
                                ? 'cursor-not-allowed bg-gray-700 dark:text-gray-900'
                                : 'hover:bg-gray-900 active:bg-blue-100',
                            isSelected(option) ? 'bg-gray-900 font-medium text-blue-800' : 'dark:text-gray-50',
                        ]" @click="selectOption(option)">
                            <div class="flex items-center gap-3">
                                <img v-if="option.img" :src="option.img" alt="" class="h-6 w-6 rounded-full object-cover" />
                                <div class="flex flex-col">
                                    <span>{{ option.label }}</span>
                                    <span v-if="option.preview" class="text-xs text-gray-400">
                                        {{ option.preview }}
                                    </span>
                                </div>
                            </div>

                            <svg v-if="isMultiple && isSelected(option)" class="h-4 w-4 text-blue-600"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                    clip-rule="evenodd" />
                            </svg>
                        </li>
                    </ul>
                </div>

                <div v-if="!filteredOptions.length && !loading" class="p-4 text-center text-sm text-gray-500">
                    No se encontraron resultados.
                </div>

                <!-- Indicador de virtual scrolling (debug) -->
                <div v-if="props.virtualScroll && filteredOptions.length > props.virtualScrollVisibleItems"
                    class="px-4 py-2 text-xs text-gray-500 border-t">
                    Mostrando {{ visibleRange.start + 1 }}-{{ visibleRange.end }} de {{ filteredOptions.length }}
                    opciones
                </div>
            </div>
        </div>
    </div>
</template>