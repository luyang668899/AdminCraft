<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{ $t('ecommerce.category.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <el-tree ref="treeRef" :data="treeData" :props="treeProps" node-key="id" default-expand-all @node-click="handleNodeClick">
                <template #default="{ node, data }">
                    <div class="flex items-center justify-between w-full">
                        <span>{{ node.label }}</span>
                        <div class="flex items-center gap-2">
                            <el-button size="small" @click.stop="handleEdit(data)">
                                {{ $t('common.edit') }}
                            </el-button>
                            <el-button size="small" type="danger" @click.stop="handleDelete(data.id)">
                                {{ $t('common.delete') }}
                            </el-button>
                        </div>
                    </div>
                </template>
            </el-tree>
        </el-card>

        <!-- 添加/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="100px">
                <el-form-item :label="$t('ecommerce.category.parent')">
                    <el-select v-model="form.pid" placeholder="{{ $t('ecommerce.category.selectParent') }}">
                        <el-option v-for="(value, key) in categoryList" :key="key" :label="value" :value="Number(key)" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('ecommerce.category.name')" required>
                    <el-input v-model="form.name" placeholder="{{ $t('ecommerce.category.enterName') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.category.alias')">
                    <el-input v-model="form.alias" placeholder="{{ $t('ecommerce.category.enterAlias') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.category.sort')">
                    <el-input-number v-model="form.sort" :min="0" :max="999" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" @click="handleSubmit">
                        {{ $t('common.submit') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive, computed } from 'vue'
import { categoryApi } from '/@/api/backend/ecommerce'
import { ElMessage } from 'element-plus'

const treeRef = ref()
const treeData = ref<any[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('')
const form = reactive({
    id: 0,
    pid: 0,
    name: '',
    alias: '',
    sort: 0,
})
const categoryList = ref<Record<string, string>>({})

const treeProps = {
    children: 'children',
    label: 'name',
}

// 加载分类树
const loadTree = async () => {
    try {
        const response = await categoryApi.getTree()
        treeData.value = response.data
    } catch (error) {
        ElMessage.error('获取分类树失败')
    }
}

// 加载分类下拉列表
const loadCategoryList = async () => {
    try {
        const response = await categoryApi.getTree()
        const list: Record<string, string> = {}

        const traverse = (data: any[], level = 0) => {
            data.forEach((item) => {
                const prefix = '├─'.padStart(level * 2 + 2, '　')
                list[item.id] = prefix + item.name
                if (item.children && item.children.length > 0) {
                    traverse(item.children, level + 1)
                }
            })
        }

        traverse(response.data)
        categoryList.value = list
    } catch (error) {
        ElMessage.error('获取分类列表失败')
    }
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加分类'
    Object.assign(form, {
        id: 0,
        pid: 0,
        name: '',
        alias: '',
        sort: 0,
    })
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (data: any) => {
    dialogTitle.value = '编辑分类'
    Object.assign(form, data)
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        const response = await categoryApi.delete(id)
        ElMessage.success(response.msg)
        loadTree()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '删除失败')
    }
}

// 处理提交
const handleSubmit = async () => {
    try {
        let response
        if (form.id === 0) {
            response = await categoryApi.add(form)
        } else {
            response = await categoryApi.edit(form.id, form)
        }
        ElMessage.success(response.msg)
        dialogVisible.value = false
        loadTree()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

// 处理节点点击
const handleNodeClick = (data: any) => {
    console.log('点击节点:', data)
}

onMounted(() => {
    loadTree()
    loadCategoryList()
})
</script>

<style scoped>
.app-container {
    padding: 20px;
}
</style>
