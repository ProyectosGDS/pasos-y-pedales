<script setup>
import { ref, computed, watch } from 'vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({

    modelValue: {
        type: [File, null],
        default: null,
    },
    icon: {
        type: String,
        default: 'cloud-arrow-up',
    },
    accept: {
        type: String,
        default: '*',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    finishProcess: {
        type: Boolean,
        default: false,
    },
    description: {
        type: String,
        default: 'SVG, PNG, JPG or GIF (MAX. 800x400px)',
    },
})

const fileInfo = ref(null)
const previewUrl = ref(null)
const isDragOver = ref(false)

const fileInputRef = ref(null)

const finish = computed(() => props.finishProcess)

watch(() => props.modelValue, (newFile) => {
    if (!newFile) {
        clearInternalState()
    }
})

const handleFile = (files) => {
    if (!files || files.length === 0) return

    const selectedFile = files[0]
    

    emit('update:modelValue', selectedFile)


    fileInfo.value = {
        success: true,
        name: selectedFile.name,
        size: Math.round(selectedFile.size / 1024),
        type: selectedFile.type,
    }


    if (selectedFile.type.startsWith('image/')) {
        previewUrl.value = URL.createObjectURL(selectedFile)
    } else {
        previewUrl.value = null
    }



}

const handleFileChange = (event) => {
    handleFile(event.target.files)
}

const handleDrop = (event) => {
    event.preventDefault()
    isDragOver.value = false
    handleFile(event.dataTransfer.files)
}

const handleDragOver = (event) => {
    event.preventDefault()
    isDragOver.value = true
}

const handleDragLeave = () => {
    isDragOver.value = false
}

function clearFile() {
    emit('update:modelValue', null)
    clearInternalState()
}

function clearInternalState() {
    fileInfo.value = null
    previewUrl.value = null
    if (fileInputRef.value) {
        fileInputRef.value.value = ''
    }
}

watch(finish, (newVal) => {
    if (newVal) {
        clearFile()
    }
})

const fileIcon = computed(() => {
    if (!fileInfo.value) return 'file'
    const type = fileInfo.value.type

    if (type.includes('pdf')) return 'file-pdf'
    if (type.includes('word')) return 'file-word'
    if (type.includes('excel') || type.includes('spreadsheet')) return 'file-excel'
    if (type.includes('zip') || type.includes('rar')) return 'file-zipper'
    return 'file'
})

if (props.modelValue instanceof File) {
    handleFile([props.modelValue])
}
</script>

---

<template>
    <div class="flex flex-col items-center justify-center ">
        <label
            class="relative flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200"
            :class="[
                isDragOver
                    ? 'border-blue-500 bg-blue-50 dark:bg-gray-600'
                    : 'border-gray-300 bg-gray-50 dark:bg-gray-700',
                'dark:hover:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500'
            ]" 
            @drop="handleDrop" 
            @dragover="handleDragOver" 
            @dragleave="handleDragLeave">
            
            <div v-if="!fileInfo" class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                <Icon :icon="props.icon" class="text-4xl mb-2 text-gray-500" />
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-semibold">Click to upload</span>
                    or drag and drop
                </p>
                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-1">
                    {{ props.description }}
                </p>
            </div>

            <div v-else class="flex flex-col items-center gap-2" :title="fileInfo.name + '-' + fileInfo.size + ' KB'">
                <div
                    class="w-24 h-24 flex items-center justify-center bg-white border rounded-md overflow-hidden shadow-sm">
                    <img v-if="previewUrl" :src="previewUrl" alt="preview" class="object-cover object-center w-full h-full" />
                    <Icon v-else :icon="fileIcon" class="text-5xl text-gray-500" />
                </div>
                <!-- <p class="text-xs text-gray-700 dark:text-gray-300 max-w-full truncate px-4">{{ fileInfo.name }}</p> -->
                <Icon @click.stop="clearFile" icon="xmark" class="rounded-full h-5 w-5 bg-red-500 px-0.5 py-1 text-xs absolute right-0 -top-0.5 text-white cursor-pointer" />
            </div>

            <input ref="fileInputRef" @change="handleFileChange" type="file" class="hidden" :accept="props.accept"
                :disabled="props.processing" />
        </label>
    </div>
</template>