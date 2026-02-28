<template>
    <div class="app-container">
        <el-card class="box-card">
            <template #header>
                <div class="card-header">
                    <span>数据权限管理</span>
                    <el-button type="primary" @click="handleAdd" size="small">
                        <el-icon><Plus /></el-icon>
                        添加
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="角色">
                    <el-select v-model="searchForm.group_id" placeholder="请选择角色">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="表名">
                    <el-input v-model="searchForm.table_name" placeholder="请输入表名" style="width: 200px" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="dataList" style="width: 100%" border>
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="group.name" label="角色" width="180" />
                <el-table-column prop="table_name" label="表名" width="180" />
                <el-table-column prop="field_name" label="字段名" width="150" />
                <el-table-column prop="rule_type" label="规则类型" width="120">
                    <template #default="scope">
                        <el-tag :type="getRuleTypeTagType(scope.row.rule_type)">
                            {{ getRuleTypeText(scope.row.rule_type) }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="rule_value" label="规则值" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="scope">
                        <el-switch
                            v-model="scope.row.status"
                            @change="handleStatusChange(scope.row)"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                        />
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建时间" width="180" />
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.id)"> 删除 </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <el-pagination
                v-model:current-page="pagination.current"
                v-model:page-size="pagination.size"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper"
                :total="pagination.total"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                class="mt-4"
            />
        </el-card>

        <!-- 添加/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" :rules="rules" ref="formRef" label-width="100px">
                <el-form-item label="角色" prop="group_id">
                    <el-select v-model="form.group_id" placeholder="请选择角色" style="width: 100%">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="表名" prop="table_name">
                    <el-input v-model="form.table_name" placeholder="请输入表名" />
                </el-form-item>
                <el-form-item label="字段名" prop="field_name">
                    <el-input v-model="form.field_name" placeholder="请输入字段名" />
                </el-form-item>
                <el-form-item label="规则类型" prop="rule_type">
                    <el-select v-model="form.rule_type" placeholder="请选择规则类型" style="width: 100%">
                        <el-option label="等于" value="equal" />
                        <el-option label="包含" value="in" />
                        <el-option label="模糊" value="like" />
                        <el-option label="范围" value="between" />
                    </el-select>
                </el-form-item>
                <el-form-item label="规则值" prop="rule_value">
                    <el-input v-model="form.rule_value" placeholder="请输入规则值" :type="'textarea'" :rows="3" />
                    <el-form-item v-if="form.rule_type === 'in'" class="text-xs text-gray-500 mt-1"> 提示：多个值用逗号分隔 </el-form-item>
                    <el-form-item v-if="form.rule_type === 'between'" class="text-xs text-gray-500 mt-1">
                        提示：两个值用逗号分隔，如：1,100
                    </el-form-item>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch v-model="form.status" active-color="#13ce66" inactive-color="#ff4949" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { dataPermissionApi } from '/@/api/backend/permission'

// 类型定义
interface Group {
    id: number
    name: string
}

interface DataPermission {
    id: number
    group_id: string
    table_name: string
    field_name: string
    rule_type: string
    rule_value: string
    status: boolean
    group?: {
        name: string
    }
}

// 状态
const loading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('添加数据权限')
const formRef = ref<any>()

// 数据
const dataList = ref<DataPermission[]>([])
const groups = ref<Group[]>([])

// 表单
const form = reactive<DataPermission>({
    id: 0,
    group_id: '',
    table_name: '',
    field_name: '',
    rule_type: '',
    rule_value: '',
    status: true,
})

// 搜索表单
const searchForm = reactive({
    group_id: '',
    table_name: '',
})

// 分页
const pagination = reactive({
    current: 1,
    size: 10,
    total: 0,
})

// 验证规则
const rules = reactive({
    group_id: [{ required: true, message: '请选择角色', trigger: 'change' }],
    table_name: [{ required: true, message: '请输入表名', trigger: 'blur' }],
    field_name: [{ required: true, message: '请输入字段名', trigger: 'blur' }],
    rule_type: [{ required: true, message: '请选择规则类型', trigger: 'change' }],
    rule_value: [{ required: true, message: '请输入规则值', trigger: 'blur' }],
})

// 初始化
onMounted(() => {
    getGroups()
    getDataList()
})

// 获取角色列表
const getGroups = async () => {
    try {
        const res = await dataPermissionApi.getGroups()
        if (res.code === 1) {
            groups.value = res.data.groups
        }
    } catch (error) {
        ElMessage.error('获取角色列表失败')
    }
}

// 获取数据列表
const getDataList = async () => {
    loading.value = true
    try {
        const params = {
            page: pagination.current,
            limit: pagination.size,
            ...searchForm,
        }
        const res = await dataPermissionApi.getList(params)
        if (res.code === 1) {
            dataList.value = res.data.list
            pagination.total = res.data.total
        }
    } catch (error) {
        ElMessage.error('获取数据列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    pagination.current = 1
    getDataList()
}

// 重置
const resetForm = () => {
    searchForm.group_id = ''
    searchForm.table_name = ''
    pagination.current = 1
    getDataList()
}

// 分页大小变化
const handleSizeChange = (size: number) => {
    pagination.size = size
    getDataList()
}

// 当前页码变化
const handleCurrentChange = (current: number) => {
    pagination.current = current
    getDataList()
}

// 添加
const handleAdd = () => {
    dialogTitle.value = '添加数据权限'
    form.id = 0
    form.group_id = ''
    form.table_name = ''
    form.field_name = ''
    form.rule_type = ''
    form.rule_value = ''
    form.status = true
    dialogVisible.value = true
}

// 编辑
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑数据权限'
    Object.assign(form, row)
    dialogVisible.value = true
}

// 删除
const handleDelete = (id: number) => {
    ElMessageBox.confirm('确定要删除这条数据吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
    }).then(async () => {
        try {
            const res = await dataPermissionApi.del([id])
            if (res.code === 1) {
                ElMessage.success('删除成功')
                getDataList()
            }
        } catch (error) {
            ElMessage.error('删除失败')
        }
    })
}

// 状态变更
const handleStatusChange = async (row: any) => {
    try {
        const res = await dataPermissionApi.edit({
            id: row.id,
            status: row.status,
        })
        if (res.code !== 1) {
            row.status = !row.status
            ElMessage.error('更新状态失败')
        }
    } catch (error) {
        row.status = !row.status
        ElMessage.error('更新状态失败')
    }
}

// 提交
const handleSubmit = async () => {
    if (!formRef.value) return
    await formRef.value.validate(async (valid: boolean) => {
        if (valid) {
            try {
                let res
                if (form.id) {
                    res = await dataPermissionApi.edit(form)
                } else {
                    res = await dataPermissionApi.add(form)
                }
                if (res.code === 1) {
                    ElMessage.success(form.id ? '编辑成功' : '添加成功')
                    dialogVisible.value = false
                    getDataList()
                }
            } catch (error) {
                ElMessage.error(form.id ? '编辑失败' : '添加失败')
            }
        }
    })
}

// 获取规则类型标签类型
const getRuleTypeTagType = (type: string | undefined): 'primary' | 'success' | 'warning' | 'info' | 'danger' => {
    const types: Record<string, 'primary' | 'success' | 'warning' | 'info' | 'danger'> = {
        equal: 'primary',
        in: 'success',
        like: 'warning',
        between: 'info',
    }
    return types[type || ''] || 'primary'
}

// 获取规则类型文本
const getRuleTypeText = (type: string | undefined) => {
    const types: Record<string, string> = {
        equal: '等于',
        in: '包含',
        like: '模糊',
        between: '范围',
    }
    return types[type || ''] || type || ''
}
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
