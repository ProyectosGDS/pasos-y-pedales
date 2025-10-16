<script setup>
    import axios from 'axios'
    import { ref, onMounted, computed, watchEffect } from 'vue'
    import Table from '../Table.vue'
    import { onClickOutside } from '@vueuse/core'
    import { useGlobalStore } from '@/stores/global'
    import LoadingBar from '../LoadingBar.vue'
import { formatVal, getNestedValue } from '@/helpers'

    const global = useGlobalStore()

    const props = defineProps({
        headers : {
            type : Array,
            default : () => [],
            required : true
        },
        url : {
            type : String,
            default : '',
            required : true
        },
        multiselect : {
            type : Boolean,
            default : false
        },
        excel : {
            type : Boolean,
            default : true,
        },
        pdf : {
            type : Boolean,
            default : true,
        }
    })

    const emits = defineEmits(['selected'])

    const data = ref([])
    const loading = ref({
        fetch : false,
        export : false
    })
    const pagination = ref({
        page : 1,
        per_page : 10,
        total : null,
        last_page : null,
        from : null,
        to : null,
        current_page : null
    })
    const selectedItems = ref([])
    const search = ref('')
    const filters = ref([
        { field : '', operator : '', value : '' }
    ])

    const operator = ['=', '!=', '>', '<', '>=', '<=','in','not in', 'null', 'not null', 'between', 'not between', 'like', 'not like']
    const openFilters = ref(false)
    const hoveredColumn = ref(null);
    const target = ref(null)

    const fields = computed(() => {
        return props.headers.map(header => {
            if(header.exclude) return
            if(header.key == 'actions') return
            return header.key.toUpperCase()
        })
    })

    const sortData = ref({
        field_first : fields.value[0],
        field : fields.value[0],
        direction : 'asc'
    })

    const processedFilters = computed(() => {
        return filters.value.map(filter => {
            const { operator, value } = filter
            const arrayOperators = ['between', 'not between', 'in', 'not in']

            if (arrayOperators.includes(operator) && typeof value === 'string') {
                return {
                    ...filter,
                    value: convertToArray(value)
                }
            }
            return filter
        })
    })

    const convertToArray = (value) => {
        return value.split(',').map(item => {
            item = item.trim()

            if (!isNaN(item)) {
                const num = Number(item)
                return num.toString() === item ? num : item
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(item)) {
                return item
            }

            return item
        })
    }

    const fetch = async () => {
        loading.value.fetch = true
        
        try {
            const response = await axios.get(props.url,{
                params : {
                    
                    page : pagination.value.page == 0 ? pagination.value.page = 1 : pagination.value.page,
                    per_page : pagination.value.per_page,
                    sort : sortData.value,
                    searching : {
                        search : search.value,
                        fields : fields.value,
                        filters : processedFilters.value,
                    }
                }
            })
            
            const { data: rows, ...paginate } = response.data
            
            data.value = rows

            Object.assign(pagination.value,paginate)

        } catch (error) {
            console.error(error)
        } finally {
            loading.value.fetch = false
        }
    }

    const nextPage = () => {
        if(parseInt(pagination.value.page) <= parseInt(pagination.value.last_page)) {
            pagination.value.page ++
            fetch()
        }
    }

    const lastPage = () => {
        pagination.value.page = pagination.value.last_page
        fetch()
    }

    const firstPage = () => {
        pagination.value.page = 1
        fetch()
    }

    const previusPage = () => {
        if(parseInt(pagination.value.page) >= 2){
            pagination.value.page --
            fetch()
        }
    }

    const sort = (column_name) => {
        sortData.value.field = column_name
        sortData.value.direction = sortData.value.direction == 'asc' ? 'desc' : 'asc'
        fetch()
    }

    const selected = () => {
        emits('selected',selectedItems.value)
    }

    const changePerPage = () => {
        pagination.value.page = 1
        fetch()
    }

    const exportData = async (type) => {

        loading.value.export = true

        try {

            let table = document.getElementById('export-table')
            if(!table) throw new Error('No se encontró la tabla para exportar')
            table = table.cloneNode(true)
            table.removeAttribute('id')

            

            const url = window.URL.createObjectURL(new Blob([table.outerHTML],{type: 'application/vnd.ms-excel'}));

            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', 'Reporte.xls')

            document.body.appendChild(link)
            link.click();

            window.URL.revokeObjectURL(url)
            document.body.removeChild(link)


        } catch (error) {
            global.manejarError(error);

        } finally {

            loading.value.export = false
        }
    }

    const deleteFilter = (index) => {
        
        if(index == 0) {
            filters.value[0] = { field : '', operator : '', value : '' }
            return
        }

        filters.value.splice(index, 1)
    }

    onClickOutside(target, () => openFilters.value = false)

    onMounted(() => fetch())


</script>

<template>
    <div class="px-4 lg:px-7  dark:text-gray-300">

        <div class="md:flex md:items-center md:justify-between">
            <div class="inline-flex items-center px-2 py-1.5 gap-2">
                <span>Mostrar</span>
                <select 
                    @change="changePerPage()" v-model="pagination.per_page"
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
                <Input icon="search" type="search" placeholder="Buscar ..." @keypress.enter="fetch()" v-model="search" />

                <div class="flex gap-2">
                    <Drop-Down icon="filter" variant="btn-primary">
                        <div class="max-w-xs">
                            <div class="flex justify-center py-1">
                                <button @click="filters.push({ field : '', operator : '', value : '' })"
                                    class="text-xs flex items-center gap-1 cursor-pointer active:scale-95 hover:font-bold">
                                    <Icon icon="plus" class="text-green-500" />
                                    Agregar
                                </button>
                            </div>
                            <div v-for="(filter, index) in filters" :key="index" class="flex items-center gap-2 text-[9px] mb-2">
                                <select class="uppercase dark:bg-gray-700 select-normal w-20" v-model="filter.field">
                                    <option value="" selected>Campo</option>
                                    <template v-for="field in props.headers">
                                        <option v-if="!field.exclude" :value="field.key">
                                            {{ field.title }}
                                        </option>
                                    </template>
                                </select>

                                <select class="dark:bg-gray-700 select-normal" v-model="filter.operator">
                                    <option value="=" selected>=</option>
                                    <option v-for="op in operator" :key="op" :value="op">{{ op }}</option>
                                </select>

                                <input type="search" class="select-normal w-24" v-model="filter.value" placeholder="Valor">

                                <Icon v-if="index !== 0" @click="deleteFilter(index)" 
                                    icon="xmark" 
                                    class="text-red-500 hover:scale-110 cursor-pointer" />
                            </div>
                            <div class="flex justify-center">
                                <Button @click="fetch()" icon="check" text="Aplicar filtro" class="btn-primary" size="sm" :loading="loading.fetch" />
                            </div>
                        </div>
                    </Drop-Down>
                    <Button v-if="props.excel" 
                        @click="fetch()" 
                        icon="arrows-rotate" 
                        class="btn-dark" 
                        :loading="loading.fetch" 
                    />
                    <Button v-if="props.excel" 
                        @click="exportData()" 
                        icon="file-excel" 
                        class="btn-green" 
                        :loading="loading.export" 
                    />
                    <Button v-if="props.pdf" 
                        icon="file-pdf" 
                        class="btn-red" 
                        :loading="loading.export" 
                    />
                </div>
            </div>
        </div>

        <LoadingBar v-if="loading.fetch" class="h-1 bg-blue-300" />

        <!-- Vista móvil -->
        <div class="mt-4 lg:hidden grid gap-4">
            <div v-for="item in data" 
                :key="item.id" 
                class="dark:bg-gray-800 border-2 border-gray-300 dark:border-0 rounded-lg p-2">
                <table class="w-full">
                    <tr v-if="props.multiple">
                        <td colspan="2">
                            <input class="checkbox" type="checkbox" v-model="selectedRows" :value="item">
                        </td>
                    </tr>
                    <tr v-for="head in props.headers" :key="head.key" class="dark:hover:bg-gray-700 hover:bg-gray-200 rounded-lg">
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
        <div class="hidden lg:block mt-4">
            <Table id="export-table">
                <template #thead>
                    <tr>
                        <th v-if="props.multiselect"></th>
                        <th scope="col" v-for="(header,colIndex) in props.headers" @click="header.hidden ? () => {} : sort(header.key.toUpperCase())"
                            @mouseenter="hoveredColumn = colIndex "
                            @mouseleave="hoveredColumn = null"
                            :align="header.align ?? 'left'"
                            :class="[
                                header.class ?? 'uppercase text-xs',
                            ]"
                            :width="header.width ?? 'auto'"
                            :key="header.key">
    
                            <div v-if="!header.hidden" class="flex gap-1 items-center">
                                <span v-if="sortData.field === header.key.toUpperCase()">
                                    {{ sortData.direction === 'asc' ? '▲' : '▼' }}
                                </span>
                                {{ header.title }}
                                <Icon v-if="header.key != 'actions'" icon="sort" class="text-[10px]" />
                            </div>
    
                        </th>
                    </tr>
                </template>
                <template #tbody>
                    <slot name="tbody" :items="data">
                        <tr v-for="(item,index) in data" class="hover:bg-gray-800" :class="{'bg-gray-100' : selectedItems.includes(item)}" >
                            <td v-if="props.multiselect">
                                <input @change="selected()" type="checkbox" class="hover:scale-125 cursor-pointer" v-model="selectedItems" :value="item">
                            </td>
                            <td v-for="(header,colIndex) in props.headers" 
                                :key="index" 
                                :title="item[header.key]" 
                                :class="[
                                    header.class ?? 'uppercase text-xs'
                                ]"
                                :width="header.width ?? 'auto'" 
                                :align="header.align ?? 'left'" >
    
                                <slot :name="header.key" :item="item">
                                    {{ formatVal(getNestedValue(item, header.key), header.type )}}
                                </slot>
                            </td>
                        </tr>
                    </slot>
                </template>
            </Table>
        </div>

        <div class="pb-4 mt-4">
            <div class="flex flex-1 justify-between md:hidden">
                <Button @click="previusPage"
                    :disabled="pagination.page == 1"
                    class="btn-primary"
                    text="Anterior"
                />
                <Button @click="nextPage"
                    :disabled="pagination.page === pagination.last_page"
                    class="btn-primary"
                    text="Siguiente"
                />
            </div>
            <div class="hidden md:flex items-center justify-between">
                <div class="flex gap-3 text-xs">
                    <span>Mostrando</span>
                    <span>{{ pagination.from }}</span>
                    <span>al</span>
                    <span>{{ pagination.to }}</span>
                    <span>de</span>
                    <span>{{ pagination.total }}</span>
                    <span>registros</span>
                </div>
                <div class="flex select-none text-xs items-center gap-2 text-gray-500">
                    <Icon @click="firstPage" icon="fas fa-backward-fast" class="icon-btn text-lg" title="Primera" />
                    <Icon @click="previusPage" icon="fas fa-caret-left" class="icon-btn text-lg" title="Anterior" />
                    <span>Página</span>
                    <input type="number" min="1" :max="pagination.lastPage" v-model="pagination.page" @keypress.enter="fetch" class="w-14 text-center focus:outline-none">
                    <span> / </span>
                    <input type="text" v-model="pagination.last_page" class="w-14 text-center focus:outline-none" readonly>
                    <Icon @click="nextPage" icon="fas fa-caret-right" class="icon-btn text-lg" title="Siguiente"/>
                    <Icon @click="lastPage" icon="fas fa-forward-fast" class="icon-btn text-lg" title="Última" />
                </div>
                <div></div>
            </div>
        </div>
        <LoadingBar v-if="loading.fetch && pagination.per_page >= 25" class="h-1 bg-blue-300" />
    </div>
</template>

<style scoped>
    @reference 'tailwindcss';
    
    th {
        @apply px-6 py-3;
    }

    td {
        @apply px-6 py-4;
    }

    .select-normal {
        @apply border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 outline-none cursor-pointer;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.2s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }

</style>