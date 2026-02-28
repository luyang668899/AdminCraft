<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>仪表盘管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建仪表盘
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="仪表盘名称">
                    <el-input v-model="searchForm.name" placeholder="请输入仪表盘名称" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="dashboardList" style="width: 100%">
                <el-table-column prop="dashboard_id" label="仪表盘ID" width="80" />
                <el-table-column prop="name" label="仪表盘名称" />
                <el-table-column prop="description" label="描述" />
                <el-table-column prop="creator.username" label="创建人" width="120" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="200">
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="handleReports(scope.row.dashboard_id)"> 报表管理 </el-button>
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.dashboard_id)"> 删除 </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <el-pagination
                v-if="total > 0"
                :current-page="page"
                :page-size="limit"
                :total="total"
                style="margin-top: 20px"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
            />
        </el-card>

        <!-- 新建/编辑仪表盘对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="仪表盘名称" required>
                    <el-input v-model="form.name" placeholder="请输入仪表盘名称" />
                </el-form-item>
                <el-form-item label="描述">
                    <el-input v-model="form.description" type="textarea" :rows="4" placeholder="请输入仪表盘描述" />
                </el-form-item>
                <el-form-item label="配置">
                    <el-input v-model="form.config" type="textarea" :rows="6" placeholder="请输入仪表盘配置（JSON格式）" />
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
import { dashboardApi } from '/@/api/backend/analytics'

const loading = ref(false)
const dashboardList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    name: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建仪表盘')
const form = reactive({
    dashboard_id: '',
    name: '',
    description: '',
    config: '',
})

// 获取仪表盘列表
const getDashboardList = async () => {
    loading.value = true
    try {
        const response = await dashboardApi.getList({
            page: page.value,
            limit: limit.value,
            name: searchForm.name,
        })
        dashboardList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取仪表盘列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getDashboardList()
}

// 重置
const resetForm = () => {
    searchForm.name = ''
    page.value = 1
    getDashboardList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getDashboardList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getDashboardList()
}

// 新建仪表盘
const handleAdd = () => {
    dialogTitle.value = '新建仪表盘'
    form.dashboard_id = ''
    form.name = ''
    form.description = ''
    form.config = ''
    dialogVisible.value = true
}

// 编辑仪表盘
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑仪表盘'
    form.dashboard_id = row.dashboard_id
    form.name = row.name
    form.description = row.description
    form.config = JSON.stringify(row.config)
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.dashboard_id) {
            // 更新仪表盘
            await dashboardApi.update({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('更新仪表盘成功')
        } else {
            // 创建仪表盘
            await dashboardApi.create({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('创建仪表盘成功')
        }
        dialogVisible.value = false
        getDashboardList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除仪表盘
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该仪表盘吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await dashboardApi.delete(id)
        ElMessage.success('删除仪表盘成功')
        getDashboardList()
    } catch (error) {
        // 取消删除
    }
}

// 报表管理
const handleReports = (dashboardId: number) => {
    // 跳转到报表管理页面
    window.location.href = `/admin/analytics/report/index/${dashboardId}`
}

onMounted(() => {
    getDashboardList()
})
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dialog-footer {
    display: flex;
    justify-content: flex-end;
}
</style>
