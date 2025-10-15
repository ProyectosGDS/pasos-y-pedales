<script setup>
import axios from 'axios'
import { ref, computed, watch, onMounted } from 'vue'

import Table from '../Table.vue'
import LoadingBar from '../LoadingBar.vue'
import { formatVal, getNestedValue, handleError } from '@/helpers'

// -------------PROPERTIES--------------
const search = ref('')
const currentPage = ref(1)
const rowsPerPage = ref(10) 
const sortColumn = ref(null)
const sortDir = ref('asc')
const sortType = ref(false)
const loadingExportData = ref(false)
const selectedRows = ref([])
const selectAll = ref(null)
const filters = ref([{ field: '', value: '', operator: '=' }]) 

const emit = defineEmits(['selectedRows'])

const props = defineProps({
    headers: {
        type: Array,
        default: () => [],
        required: true,
    },
    data: {
        type: Array,
        default: () => [],
        required: true,
    },
    loading: {
        type: Boolean,
        default: false
    },
    excel: {
        type: Boolean,
        default: true
    },
    pdf: {
        type: Boolean,
        default: true
    },
    multiple: {
        type: Boolean,
        default: false
    },
    itemsSelected: {
        type: Array,
        default: () => []
    },
    rowsPerPageProp: {
        type: Number,
        default: 10
    }
})

// -------------HELPERS--------------
const isDate = (value) => {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/
    return dateRegex.test(value) && !isNaN(parseDate(value))
}

const parseDate = (value) => {
    const [year, month, day] = value.split('-').map(Number)
    return new Date(year, month - 1, day)
}

// -------------COMPUTED--------------
const searchables = computed(() => 
    props.headers.map(el => el.key.toLowerCase().trim())
)

const filteredItems = computed(() => {
    const searchTerms = search.value.toLowerCase().trim().split(';').map(term => term.trim())

    return props.data.filter((item) => {
        // Aplicar filtros avanzados
        const passesFilters = filters.value.every((filter) => {
            if (!filter.field || !filter.value) return true

            const itemValueRaw = getNestedValue(item, filter.field)
            const filterValue = filter.value.trim()
            const filterValueLower = filterValue.toLowerCase()

            // Valores para operadores especiales
            const filterValuesArray = filterValue.split(',').map(v => v.trim()).filter(v => v !== '')
            const betweenValues = filterValue.split('-').map(v => v.trim()).filter(v => v !== '')

            // Determinar tipo de dato
            const isItemNumeric = !isNaN(parseFloat(itemValueRaw)) && isFinite(itemValueRaw)
            const isFilterNumeric = !isNaN(parseFloat(filterValue)) && isFinite(filterValue)

            // Comparación numérica
            if (isItemNumeric && isFilterNumeric) {
                const itemNumber = parseFloat(itemValueRaw)
                const filterNumber = parseFloat(filterValue)
                const filterNumbersArray = filterValuesArray.map(Number).filter(n => !isNaN(n))

                if (filter.operator === 'between' && betweenValues.length === 2) {
                    const [valA, valB] = betweenValues.map(Number)
                    return itemNumber >= valA && itemNumber <= valB
                }

                switch (filter.operator) {
                    case '=': return itemNumber === filterNumber
                    case '!=': return itemNumber !== filterNumber
                    case '>': return itemNumber > filterNumber
                    case '<': return itemNumber < filterNumber
                    case '>=': return itemNumber >= filterNumber
                    case '<=': return itemNumber <= filterNumber
                    case 'in': return filterNumbersArray.includes(itemNumber)
                    case 'not in': return !filterNumbersArray.includes(itemNumber)
                    default: return false
                }
            }

            // Comparación de fechas
            if (isDate(String(itemValueRaw)) && isDate(filterValue)) {
                const itemDate = parseDate(String(itemValueRaw))
                const filterDate = parseDate(filterValue)

                if (filter.operator === 'between' && betweenValues.length === 2) {
                    const dateA = parseDate(betweenValues[0])
                    const dateB = parseDate(betweenValues[1])
                    return itemDate >= dateA && itemDate <= dateB
                }

                switch (filter.operator) {
                    case '=': return itemDate.getTime() === filterDate.getTime()
                    case '!=': return itemDate.getTime() !== filterDate.getTime()
                    case '>': return itemDate > filterDate
                    case '<': return itemDate < filterDate
                    case '>=': return itemDate >= filterDate
                    case '<=': return itemDate <= filterDate
                    default: return false
                }
            }

            // Comparación de texto
            const itemText = String(itemValueRaw).toLowerCase()
            const filterTextArray = filterValuesArray.map(v => v.toLowerCase())

            switch (filter.operator) {
                case '=': return itemText === filterValueLower
                case '!=': return itemText !== filterValueLower
                case 'contains': return itemText.includes(filterValueLower)
                case 'in': return filterTextArray.includes(itemText)
                case 'not in': return !filterTextArray.includes(itemText)
                default: return false
            }
        })

        // Aplicar búsqueda de texto
        const passesSearch = searchTerms.every((searchTerm) => {
            return searchables.value.some((column) => {
                const value = getNestedValue(item, column)
                return String(value).toLowerCase().includes(searchTerm)
            })
        })

        return passesFilters && passesSearch
    })
})

const sortedData = computed(() => {
    if (!sortColumn.value) return filteredItems.value

    return [...filteredItems.value].sort((a, b) => {
        const valA = getNestedValue(a, sortColumn.value)
        const valB = getNestedValue(b, sortColumn.value)

        if (sortType.value === 'numeric') {
            const numA = Number(valA)
            const numB = Number(valB)
            return sortDir.value === 'asc' ? numA - numB : numB - numA
        }

        const strA = String(valA)
        const strB = String(valB)
        return sortDir.value === 'asc' ? strA.localeCompare(strB) : strB.localeCompare(strA)
    })
})

const totalPages = computed(() => 
    Math.ceil(sortedData.value.length / rowsPerPage.value)
)

const startIndex = computed(() => 
    (currentPage.value - 1) * rowsPerPage.value
)

const endIndex = computed(() => 
    Math.min(startIndex.value + rowsPerPage.value, sortedData.value.length)
)

const paginatedData = computed(() => 
    sortedData.value.slice(startIndex.value, endIndex.value)
)

const displayedPages = computed(() => {
    const totalDisplayed = 6
    const half = Math.floor(totalDisplayed / 2)
    let start = Math.max(currentPage.value - half, 1)
    let end = Math.min(start + totalDisplayed - 1, totalPages.value)

    if (end - start + 1 < totalDisplayed) {
        start = Math.max(end - totalDisplayed + 1, 1)
    }

    return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// -------------WATCHERS--------------
watch([search, filters], () => {
    currentPage.value = 1
}, { deep: true })

watch(selectedRows, () => {
    emit('selectedRows', selectedRows.value)
}, { deep: true })

// -------------METHODS--------------
const setCurrentPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
    }
}

const sort = (column, type) => {
    sortType.value = type
    if (sortColumn.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortColumn.value = column
        sortDir.value = 'asc'
    }
}

const resetPage = () => {
    currentPage.value = 1
}

const allSelectedRows = () => {
    if (selectAll.value?.checked) {
        // Agregar solo los items que no están ya seleccionados
        const newItems = paginatedData.value.filter(
            item => !selectedRows.value.includes(item)
        )
        selectedRows.value.push(...newItems)
    } else {
        // Remover solo los items de la página actual
        selectedRows.value = selectedRows.value.filter(
            item => !paginatedData.value.includes(item)
        )
    }
}

const exportData = async () => {
    if (sortedData.value.length === 0) return

    loadingExportData.value = true
    try {
        const response = await axios.post('exportar-excel', {
            columns: props.headers,
            data: sortedData.value
        }, {
            responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `export_${Date.now()}.xlsx`)
        document.body.appendChild(link)
        link.click()
        window.URL.revokeObjectURL(url)
        document.body.removeChild(link)
    } catch (error) {
        console.error(error)
    } finally {
        loadingExportData.value = false
    }
}

const addFilter = () => {
    filters.value.push({ field: '', value: '', operator: '=' })
}

const removeFilter = (index) => {
    if (index > 0) {
        filters.value.splice(index, 1)
    }
}

onMounted(() => {
    selectedRows.value = [...props.itemsSelected]
    rowsPerPage.value = props.rowsPerPageProp
})
</script>

<template>
    <section class="px-4 lg:px-7 grid gap-4 text-black dark:text-gray-300">
        <div class="md:flex md:items-center md:justify-between">
            <div class="inline-flex items-center px-2 py-1.5 gap-2">
                <span>Mostrar</span>
                <select 
                    v-model.number="rowsPerPage" 
                    @change="resetPage"
                    class="dark:bg-gray-900 cursor-pointer text-center w-full focus:outline-none ring-0">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                    <option :value="500">500</option>
                </select>
                <span>registros</span>
            </div>
            
            <div class="flex items-center gap-2">
                <Input icon="search" type="search" placeholder="Buscar ..." v-model="search" />

                <div class="flex gap-2">
                    <Drop-Down icon="filter" variant="btn-primary">
                        <div class="max-w-xs">
                            <div class="flex justify-center py-1">
                                <button @click="addFilter"
                                    class="text-xs flex items-center gap-1 cursor-pointer active:scale-95 hover:font-bold">
                                    <Icon icon="plus" class="text-green-500" />
                                    Agregar
                                </button>
                            </div>
                            <div v-for="(item, index) in filters" :key="index" class="flex items-center gap-2 text-xs mb-2">
                                <select class="uppercase dark:bg-gray-700 select-normal" v-model="item.field">
                                    <option value="">Campo</option>
                                    <option v-for="head in props.headers" :key="head.key" :value="head.key">
                                        {{ head.title }}
                                    </option>
                                </select>

                                <select class="dark:bg-gray-700 select-normal" v-model="item.operator">
                                    <option value="=" selected>=</option>
                                    <option value="!=">!=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
                                    <option value=">=">>=</option>
                                    <option value="<="><=</option>
                                    <option value="in">In</option>
                                    <option value="not in">Not in</option>
                                    <option value="contains">Like</option>
                                    <option value="between">Between</option>
                                </select>

                                <input type="search" class="select-normal w-30" v-model="item.value" placeholder="Valor">

                                <Icon v-if="index !== 0" @click="removeFilter(index)" 
                                    icon="xmark" 
                                    class="text-red-500 hover:scale-110 cursor-pointer" />
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">
                                * <strong>Between</strong> usa guion (-). Ej: 10-20<br>
                                * <strong>In/Not in</strong> usa coma (,). Ej: valor1,valor2,valor3
                            </p>
                        </div>
                    </Drop-Down>
                    <Button v-if="props.excel && sortedData.length > 0" 
                        @click="exportData" 
                        icon="file-excel" 
                        class="btn-green" 
                        :loading="loadingExportData" />
                    <Button v-if="props.pdf && sortedData.length > 0" 
                        icon="file-pdf" 
                        class="btn-red" 
                        :loading="loadingExportData" />
                </div>
            </div>
        </div>

        <!-- Vista móvil -->
        <div class="grid gap-4 lg:hidden py-4 w-max">
            <div v-for="item in paginatedData" :key="item.id" class="dark:bg-gray-800 p-2 rounded-xl">
                <table class="w-full">
                    <tr v-if="props.multiple">
                        <td colspan="2">
                            <input class="checkbox" type="checkbox" v-model="selectedRows" :value="item">
                        </td>
                    </tr>
                    <tr v-for="head in props.headers" :key="head.key" class="hover:bg-gray-700 rounded-lg">
                        <td class="px-4 font-semibold uppercase text-sm select-none" 
                            :width="head.width" 
                            align="left" 
                            :hidden="head.hidden">
                            {{ head.title }}
                        </td>
                        <td :align="head.align ?? 'center'" 
                            :width="head.width" 
                            :hidden="head.hidden">
                            <slot :name="head.key" :item="item">
                                <div :class="`uppercase text-xs ${head.class}`">
                                    <Icon v-if="head.icon" :icon="head.icon" />
                                    <span>{{ formatVal(getNestedValue(item, head.key), head.type) }} </span>
                                    <span>{{ head.text ?? '' }}</span>
                                </div>
                            </slot>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Vista desktop -->
        <Table class="invisible lg:visible">
            <template #thead>
                <tr>
                    <th v-if="props.multiple">
                        <input class="checkbox" type="checkbox" ref="selectAll" 
                            @change="allSelectedRows" 
                            title="Seleccionar todos">
                    </th>
                    <th v-for="head in props.headers" 
                        :key="head.key" 
                        @click="sort(head.key, head.type)" 
                        scope="col" 
                        :width="head.width" 
                        :align="head.align ?? 'left'" 
                        :hidden="head.hidden"
                        class="cursor-pointer select-none">
                        <div class="flex gap-1 items-center">
                            <span v-if="sortColumn === head.key" class="text-xs">
                                {{ sortDir === 'asc' ? '▲' : '▼' }}
                            </span>
                            {{ head.title }}
                        </div>
                    </th>
                </tr>
            </template>
            <template #tbody>
                <slot name="tbody" :items="paginatedData">
                    <tr v-for="item in paginatedData" 
                        :key="item.id" 
                        class="dark:hover:bg-gray-800 hover:bg-gray-200">
                        <td v-if="props.multiple">
                            <input class="checkbox" type="checkbox" v-model="selectedRows" :value="item">
                        </td>
                        <td v-for="head in props.headers" 
                            :key="head.key" 
                            :align="head.align ?? 'left'" 
                            :width="head.width" 
                            :hidden="head.hidden">
                            <slot :name="head.key" :item="item">
                                <div :class="`uppercase text-xs ${head.class}`">
                                    <Icon v-if="head.icon" :icon="head.icon" />
                                    <span>{{ formatVal(getNestedValue(item, head.key), head.type) }} </span>
                                    <span>{{ head.text ?? '' }}</span>
                                </div>
                            </slot>
                        </td>
                    </tr>
                </slot>
                <tr v-if="props.loading">
                    <td align="center" :colspan="props.headers.length + (props.multiple ? 1 : 0)" class="px-10">
                        <LoadingBar class="h-1 bg-color-4" />
                        <span class="animate-ping">Cargando data...</span>
                    </td>
                </tr>
                <tr v-if="sortedData.length === 0 && !props.loading">
                    <td align="center" :colspan="props.headers.length + (props.multiple ? 1 : 0)">
                        No hay datos disponibles
                    </td>
                </tr>
            </template>
        </Table>

        <!-- Paginación -->
        <div class="flex items-center justify-between pb-4">
            <div v-show="sortedData.length >= rowsPerPage && displayedPages.length > 1" 
                class="flex flex-1 justify-between md:hidden">
                <button @click="setCurrentPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="relative flex items-center rounded border border-gray-300 select-none cursor-pointer btn px-4 py-2 text-sm font-medium disabled:opacity-50">
                    Anterior
                </button>
                <button @click="setCurrentPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="relative ml-3 flex items-center rounded border border-gray-300 select-none cursor-pointer btn px-4 py-2 text-sm font-medium disabled:opacity-50">
                    Siguiente
                </button>
            </div>

            <div class="hidden md:flex md:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs text-color-4">
                        Mostrando
                        <span class="font-medium">{{ startIndex + 1 }}</span>
                        a
                        <span class="font-medium">{{ endIndex }}</span>
                        de
                        <span class="font-medium">{{ sortedData.length }}</span>
                        resultados
                    </p>
                </div>
                <div v-if="props.itemsSelected.length > 0">
                    <p class="text-xs text-color-4">
                        Seleccionados
                        <span class="font-medium">{{ props.itemsSelected.length }}</span>
                        de
                        <span class="font-medium">{{ sortedData.length }}</span>
                    </p>
                </div>
                <div v-show="sortedData.length >= rowsPerPage && displayedPages.length > 1">
                    <nav class="flex gap-x-2">
                        <button v-if="currentPage > 4" 
                            @click="setCurrentPage(1)"
                            class="cursor-pointer relative flex items-center px-3 py-1.5 font-semibold text-color-4 rounded-full hover:bg-gray-200">
                            <Icon icon="angles-left" class="text-xs" />
                        </button>
                        <button v-if="currentPage > 1" 
                            @click="setCurrentPage(currentPage - 1)"
                            class="cursor-pointer relative flex items-center px-3 py-1.5 text-sm font-semibold text-color-4 rounded-full hover:bg-gray-200">
                            <Icon icon="angle-left" class="text-xs" />
                        </button>
                        <button v-for="page in displayedPages" 
                            :key="page" 
                            @click="setCurrentPage(page)"
                            :class="page === currentPage ? 'bg-gray-200 dark:bg-gray-700' : ''"
                            class="cursor-pointer select-none relative flex items-center px-3 py-1.5 text-color-4 text-sm font-semibold rounded-full hover:bg-gray-200">
                            {{ page }}
                        </button>
                        <button v-if="currentPage < totalPages" 
                            @click="setCurrentPage(currentPage + 1)"
                            class="cursor-pointer relative flex items-center px-3 py-1.5 text-sm font-semibold text-color-4 rounded-full hover:bg-gray-200">
                            <Icon icon="angle-right" class="text-xs" />
                        </button>
                        <button v-if="currentPage < totalPages - 2" 
                            @click="setCurrentPage(totalPages)"
                            class="cursor-pointer relative flex items-center px-3 py-1.5 font-semibold text-color-4 rounded-full hover:bg-gray-200">
                            <Icon icon="angles-right" class="text-xs" />
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
    @reference 'tailwindcss';

    td {
        @apply px-6 py-4;
    }

    th {
        @apply font-bold uppercase px-6 py-3;
    }

    .select-normal {
        @apply border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 outline-none cursor-pointer;
    }

    .checkbox {
        @apply h-4 w-4 cursor-pointer accent-blue-600;
    }
</style>