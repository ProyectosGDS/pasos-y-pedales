<script setup>

    import { ref, onMounted } from 'vue'
    import { useGlobalStore } from '../stores/global'

    const global = useGlobalStore()

    const props = defineProps({
        message : {
            type : String,
            default : '',
            required : true
        },
        type : {
            type : String,
            default : '',
            required : true,
            validator : (value) => {
                return ['primary','success','danger','warning'].includes(value) 
            }
        },
        title : {
            type : String,
            default : ' Atención'
        },
    })


    let open = ref(true)
    
    onMounted(() => {
        setTimeout( () => {
            global.toasts.shift()
        }, 4000 )        
    })


</script>

<template>
    <div v-if="open" 
        class="w-full max-w-xs min-w-sm p-3 text-gray-900 bg-white rounded-lg shadow-sm dark:bg-gray-800 dark:text-gray-300 border-2"
        :class="{
                    'border-blue-500'   : props.type === 'primary',
                    'border-green-500'  : props.type === 'success',
                    'border-red-500'    : props.type === 'danger',
                    'border-orange-500' : props.type === 'warning',
                }">
        <div class="flex items-center mb-2">
            <span class="mb-1 text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ props.title }}</span>
            <button class="ms-auto -mx-1.5 -my-1.5 bg-white justify-center items-center shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                <Icon @click="open=false" icon="xmark" />
            </button>
        </div>
        <div class="flex items-center">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
                :class="{
                    'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200'   : props.type === 'primary',
                    'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200'  : props.type === 'success',
                    'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200'    : props.type === 'danger',
                    'text-orange-500 bg-orange-100 dark:bg-orange-700 dark:text-orange-200' : props.type === 'warning',
                }">
                <Icon v-if="props.type === 'primary'" icon="fire" class="text-xl"/>
                <Icon v-else-if="props.type === 'success'" icon="circle-check" class="text-xl"/>
                <Icon v-else-if="props.type === 'danger'" icon="circle-xmark" class="text-xl"/>
                <Icon v-else-if="props.type === 'warning'" icon="circle-exclamation" class="text-xl"/>
            </div>
            <div class="ms-3 text-sm font-normal">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ props.message }}</div>
            </div>
        </div>
    </div>
</template>