<script setup>
    defineOptions({ inheritAttrs: false })
</script>
<template>
    <div class="shadow-md sm:rounded-lg">
        <table v-bind="$attrs" class="w-full text-sm text-left rtl:text-right table-auto">
            <thead class="text-xs uppercase bg-gray-300 dark:bg-gray-700">
                <slot name="thead"></slot>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <slot name="tbody"></slot>
            </tbody>
            <tfoot>
                <slot name="tfoot"></slot>
            </tfoot>
        </table>
    </div>
</template>
