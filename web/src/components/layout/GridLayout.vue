<template>
    <div class="grid-layout" :class="{ gutter: gutter }">
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    gutter?: number
}>()
</script>

<style scoped>
.grid-layout {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -8px;
}

.grid-layout.gutter {
    margin: 0 -16px;
}

.grid-layout > * {
    padding: 8px;
}

.grid-layout.gutter > * {
    padding: 16px;
}
</style>
