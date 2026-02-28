<template>
    <div class="app-container">
        <el-card class="box-card">
            <template #header>
                <div class="card-header">
                    <span>权限继承管理</span>
                    <el-button type="primary" @click="handleAdd" size="small">
                        <el-icon><Plus /></el-icon>
                        添加
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="父角色">
                    <el-select v-model="searchForm.parent_group_id" placeholder="请选择父角色">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="子角色">
                    <el-select v-model="searchForm.child_group_id" placeholder="请选择子角色">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">查询</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="dataList" style="width: 100%" border>
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="parentGroup.name" label="父角色" width="180" />
                <el-table-column prop="childGroup.name" label="子角色" width="180" />
                <el-table-column prop="inherit_type" label="继承类型" width="120">
                    <template #default="scope">
                        <el-tag :type="scope.row.inherit_type === 'all' ? 'primary' : 'success'">
                            {{ scope.row.inherit_type === 'all' ? '全部' : '自定义' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="inherit_rules" label="继承规则" />
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
                <el-form-item label="父角色" prop="parent_group_id">
                    <el-select v-model="form.parent_group_id" placeholder="请选择父角色" style="width: 100%">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="子角色" prop="child_group_id">
                    <el-select v-model="form.child_group_id" placeholder="请选择子角色" style="width: 100%">
                        <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="继承类型" prop="inherit_type">
                    <el-select v-model="form.inherit_type" placeholder="请选择继承类型" style="width: 100%">
                        <el-option label="继承全部权限" value="all" />
                        <el-option label="继承指定权限" value="custom" />
                    </el-select>
                </el-form-item>
                <el-form-item v-if="form.inherit_type === 'custom'" label="继承规则" prop="inherit_rules">
                    <el-input v-model="form.inherit_rules" placeholder="请输入权限ID，多个用逗号分隔" :type="'textarea'" :rows="3" />
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
import { permissionInheritApi } from '/@/api/backend/permission'

// 类型定义
interface Group {
    id: number
    name: string
}

interface PermissionInherit {
    id: number
    parent_group_id: string
    child_group_id: string
    inherit_type: string
    inherit_rules: string
    status: boolean
    parent_group?: {
        name: string
    }
    child_group?: {
        name: string
    }
}

// 状态
const loading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('添加权限继承')
const formRef = ref<any>()

// 数据
const dataList = ref<PermissionInherit[]>([])
const groups = ref<Group[]>([])

// 表单
const form = reactive<PermissionInherit>({
    id: 0,
    parent_group_id: '',
    child_group_id: '',
    inherit_type: 'all',
    inherit_rules: '',
    status: true,
})

// 搜索表单
const searchForm = reactive({
    parent_group_id: '',
    child_group_id: '',
})

// 分页
const pagination = reactive({
    current: 1,
    size: 10,
    total: 0,
})

// 验证规则
const rules = reactive({
    parent_group_id: [{ required: true, message: '请选择父角色', trigger: 'change' }],
    child_group_id: [{ required: true, message: '请选择子角色', trigger: 'change' }],
    inherit_type: [{ required: true, message: '请选择继承类型', trigger: 'change' }],
    inherit_rules: [
        {
            required: true,
            message: '请输入继承规则',
            trigger: 'blur',
            validator: (rule: any, value: string, callback: (error?: Error) => void) => {
                if (form.inherit_type === 'custom' && !value) {
                    callback(new Error('请输入继承规则'))
                } else {
                    callback()
                }
            },
        },
    ],
})

// 初始化
onMounted(() => {
    getGroups()
    getDataList()
})

// 获取角色列表
const getGroups = async () => {
    try {
        const res = await permissionInheritApi.getGroups()
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
        const res = await permissionInheritApi.getList(params)
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
    searchForm.parent_group_id = ''
    searchForm.child_group_id = ''
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
    dialogTitle.value = '添加权限继承'
    form.id = 0
    form.parent_group_id = ''
    form.child_group_id = ''
    form.inherit_type = 'all'
    form.inherit_rules = ''
    form.status = true
    dialogVisible.value = true
}

// 编辑
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑权限继承'
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
            const res = await permissionInheritApi.del([id])
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
        const res = await permissionInheritApi.edit({
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
                    res = await permissionInheritApi.edit(form)
                } else {
                    res = await permissionInheritApi.add(form)
                }
                if (res.code === 1) {
                    ElMessage.success(form.id ? '编辑成功' : '添加成功')
                    dialogVisible.value = false
                    getDataList()
                }
            } catch (error: any) {
                ElMessage.error(error.response?.data?.msg || (form.id ? '编辑失败' : '添加失败'))
            }
        }
    })
}
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
