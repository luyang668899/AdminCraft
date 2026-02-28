<template>
    <div class="advanced-search">
        <el-collapse v-model="activeNames">
            <el-collapse-item title="高级搜索" name="1">
                <el-form :model="form" :inline="true" class="mt-2">
                    <el-form-item v-for="(item, index) in fields" :key="index" :label="item.label">
                        <component
                            :is="getComponent(item.type)"
                            v-model="form[item.prop]"
                            v-bind="item.props"
                            :placeholder="item.placeholder || `请输入${item.label}`"
                        />
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">搜索</el-button>
                        <el-button @click="resetForm">重置</el-button>
                    </el-form-item>
                </el-form>
            </el-collapse-item>
        </el-collapse>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'

interface Field {
    prop: string
    label: string
    type: string
    placeholder?: string
    props?: any
}

const props = defineProps<{
    fields: Field[]
    defaultForm?: Record<string, any>
}>()

const emit = defineEmits<{
    (e: 'search', form: Record<string, any>): void
    (e: 'reset'): void
}>()

const activeNames = ref(['1'])

const form = reactive<Record<string, any>>({ ...(props.defaultForm || {}) })

const getComponent = (type: string) => {
    const componentMap: Record<string, string> = {
        input: 'el-input',
        select: 'el-select',
        date: 'el-date-picker',
        time: 'el-time-picker',
        switch: 'el-switch',
        checkbox: 'el-checkbox',
        radio: 'el-radio',
    }
    return componentMap[type] || 'el-input'
}

const handleSearch = () => {
    emit('search', { ...form })
}

const resetForm = () => {
    Object.keys(form).forEach((key) => {
        form[key] = ''
    })
    emit('reset')
}
</script>

<style scoped>
.advanced-search {
    margin-bottom: 16px;
}
</style>
