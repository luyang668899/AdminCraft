<template>
    <div
        class="grid-item"
        :class="{
            [`span-${span}`]: true,
            [`offset-${offset}`]: offset,
            [`pull-${pull}`]: pull,
            [`push-${push}`]: push,
        }"
    >
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    span?: number
    offset?: number
    pull?: number
    push?: number
}>()
</script>

<style scoped>
.grid-item {
    box-sizing: border-box;
    flex: 0 0 auto;
    width: 100%;
}

/* 24-column grid system */
.span-1 {
    width: 4.16666667%;
}
.span-2 {
    width: 8.33333333%;
}
.span-3 {
    width: 12.5%;
}
.span-4 {
    width: 16.66666667%;
}
.span-5 {
    width: 20.83333333%;
}
.span-6 {
    width: 25%;
}
.span-7 {
    width: 29.16666667%;
}
.span-8 {
    width: 33.33333333%;
}
.span-9 {
    width: 37.5%;
}
.span-10 {
    width: 41.66666667%;
}
.span-11 {
    width: 45.83333333%;
}
.span-12 {
    width: 50%;
}
.span-13 {
    width: 54.16666667%;
}
.span-14 {
    width: 58.33333333%;
}
.span-15 {
    width: 62.5%;
}
.span-16 {
    width: 66.66666667%;
}
.span-17 {
    width: 70.83333333%;
}
.span-18 {
    width: 75%;
}
.span-19 {
    width: 79.16666667%;
}
.span-20 {
    width: 83.33333333%;
}
.span-21 {
    width: 87.5%;
}
.span-22 {
    width: 91.66666667%;
}
.span-23 {
    width: 95.83333333%;
}
.span-24 {
    width: 100%;
}

/* Offset */
.offset-1 {
    margin-left: 4.16666667%;
}
.offset-2 {
    margin-left: 8.33333333%;
}
.offset-3 {
    margin-left: 12.5%;
}
.offset-4 {
    margin-left: 16.66666667%;
}
.offset-5 {
    margin-left: 20.83333333%;
}
.offset-6 {
    margin-left: 25%;
}
.offset-7 {
    margin-left: 29.16666667%;
}
.offset-8 {
    margin-left: 33.33333333%;
}
.offset-9 {
    margin-left: 37.5%;
}
.offset-10 {
    margin-left: 41.66666667%;
}
.offset-11 {
    margin-left: 45.83333333%;
}
.offset-12 {
    margin-left: 50%;
}
.offset-13 {
    margin-left: 54.16666667%;
}
.offset-14 {
    margin-left: 58.33333333%;
}
.offset-15 {
    margin-left: 62.5%;
}
.offset-16 {
    margin-left: 66.66666667%;
}
.offset-17 {
    margin-left: 70.83333333%;
}
.offset-18 {
    margin-left: 75%;
}
.offset-19 {
    margin-left: 79.16666667%;
}
.offset-20 {
    margin-left: 83.33333333%;
}
.offset-21 {
    margin-left: 87.5%;
}
.offset-22 {
    margin-left: 91.66666667%;
}
.offset-23 {
    margin-left: 95.83333333%;
}

/* Pull and Push */
.pull-1 {
    margin-right: 4.16666667%;
}
.pull-2 {
    margin-right: 8.33333333%;
}
.pull-3 {
    margin-right: 12.5%;
}
.pull-4 {
    margin-right: 16.66666667%;
}
.pull-5 {
    margin-right: 20.83333333%;
}
.pull-6 {
    margin-right: 25%;
}
.pull-7 {
    margin-right: 29.16666667%;
}
.pull-8 {
    margin-right: 33.33333333%;
}
.pull-9 {
    margin-right: 37.5%;
}
.pull-10 {
    margin-right: 41.66666667%;
}
.pull-11 {
    margin-right: 45.83333333%;
}
.pull-12 {
    margin-right: 50%;
}

.push-1 {
    margin-left: 4.16666667%;
}
.push-2 {
    margin-left: 8.33333333%;
}
.push-3 {
    margin-left: 12.5%;
}
.push-4 {
    margin-left: 16.66666667%;
}
.push-5 {
    margin-left: 20.83333333%;
}
.push-6 {
    margin-left: 25%;
}
.push-7 {
    margin-left: 29.16666667%;
}
.push-8 {
    margin-left: 33.33333333%;
}
.push-9 {
    margin-left: 37.5%;
}
.push-10 {
    margin-left: 41.66666667%;
}
.push-11 {
    margin-left: 45.83333333%;
}
.push-12 {
    margin-left: 50%;
}

/* Responsive */
@media (max-width: 768px) {
    .grid-item {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
}
</style>
