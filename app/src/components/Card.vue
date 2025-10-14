<script setup>
</script>

<template>
    <div class="rounded-lg flex flex-col shadow-lg overflow-hidden max-w-7xl mx-auto min-w-max">
        <header>
            <slot name="header"></slot>
        </header>
        <div class="flex-1">
            <slot></slot>    
        </div>
        <footer>
            <slot name="footer"></slot>
        </footer>
    </div>
</template>
