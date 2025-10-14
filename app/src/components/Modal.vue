<script setup>
    const props = defineProps({
        open : {
            type : Boolean,
            default : false
        },
        header : {
            type : String,
            default : ''
        },
        icon : {
            type : String,
            default : ''
        },
        size : {
            type : String,
            default : 'max-w-2xl'
        }
    })

</script>

<template>
    <Transition>
        <div v-if="props.open" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center  w-full md:inset-0 h-[calc(100%)] max-h-full bg-black/50">
            <div class="relative p-4 w-full max-h-full" :class="size" >
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <Icon :icon="props.icon" />
                            {{ props.header }}
                        </h3>
                        <slot name="closed"/>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <slot />
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center justify-center gap-3 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
    .v-enter-active, .v-leave-active {
        transition: opacity 0.5s ease;
    }

    .v-enter-from, .v-leave-to {
        opacity: 0;
    }
</style>
