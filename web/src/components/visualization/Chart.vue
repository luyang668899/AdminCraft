<template>
    <div class="chart-container">
        <div :id="chartId" :style="{ width: width, height: height }"></div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue'
import * as echarts from 'echarts'

const props = defineProps<{
    type: string
    data: any
    options?: any
    width?: string
    height?: string
}>()

const chartId = ref(`chart-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`)
const chart = ref<echarts.ECharts | null>(null)

const width = props.width || '100%'
const height = props.height || '400px'

const initChart = () => {
    if (chart.value) {
        chart.value.dispose()
    }

    chart.value = echarts.init(document.getElementById(chartId.value))
    updateChart()
}

const updateChart = () => {
    if (!chart.value) return

    let options = {}

    switch (props.type) {
        case 'line':
            options = {
                xAxis: {
                    type: 'category',
                    data: props.data.xAxis || [],
                },
                yAxis: {
                    type: 'value',
                },
                series: props.data.series || [],
            }
            break
        case 'bar':
            options = {
                xAxis: {
                    type: 'category',
                    data: props.data.xAxis || [],
                },
                yAxis: {
                    type: 'value',
                },
                series: props.data.series || [],
            }
            break
        case 'pie':
            options = {
                series: [
                    {
                        type: 'pie',
                        data: props.data.series || [],
                    },
                ],
            }
            break
        case 'scatter':
            options = {
                xAxis: {
                    type: 'value',
                },
                yAxis: {
                    type: 'value',
                },
                series: props.data.series || [],
            }
            break
        default:
            options = props.options || {}
    }

    chart.value.setOption(options)
}

const handleResize = () => {
    chart.value?.resize()
}

onMounted(() => {
    initChart()
    window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    chart.value?.dispose()
})

watch(
    () => props.data,
    () => {
        updateChart()
    },
    { deep: true }
)

watch(
    () => props.options,
    () => {
        updateChart()
    },
    { deep: true }
)
</script>

<style scoped>
.chart-container {
    position: relative;
}
</style>
