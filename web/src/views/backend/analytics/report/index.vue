<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>报表管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建报表
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="报表名称">
                    <el-input v-model="searchForm.name" placeholder="请输入报表名称" />
                </el-form-item>
                <el-form-item label="报表类型">
                    <el-select v-model="searchForm.type" placeholder="请选择报表类型">
                        <el-option label="表格" value="table" />
                        <el-option label="图表" value="chart" />
                        <el-option label="混合" value="mixed" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="reportList" style="width: 100%">
                <el-table-column prop="report_id" label="报表ID" width="80" />
                <el-table-column prop="name" label="报表名称" />
                <el-table-column prop="type" label="报表类型" width="100" />
                <el-table-column prop="data_source" label="数据源" />
                <el-table-column prop="creator.username" label="创建人" width="120" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="200">
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="handleCharts(scope.row.report_id)"> 图表管理 </el-button>
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.report_id)"> 删除 </el-button>
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

        <!-- 新建/编辑报表对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="报表名称" required>
                    <el-input v-model="form.name" placeholder="请输入报表名称" />
                </el-form-item>
                <el-form-item label="报表类型" required>
                    <el-select v-model="form.type" placeholder="请选择报表类型">
                        <el-option label="表格" value="table" />
                        <el-option label="图表" value="chart" />
                        <el-option label="混合" value="mixed" />
                    </el-select>
                </el-form-item>
                <el-form-item label="数据源" required>
                    <el-input v-model="form.data_source" placeholder="请输入数据源" />
                </el-form-item>
                <el-form-item label="查询语句">
                    <el-input v-model="form.query" type="textarea" :rows="4" placeholder="请输入查询语句" />
                </el-form-item>
                <el-form-item label="配置">
                    <el-input v-model="form.config" type="textarea" :rows="6" placeholder="请输入报表配置（JSON格式）" />
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
import { useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { reportApi } from '/@/api/backend/analytics'

const route = useRoute()
const dashboardId = ref(Number(route.params.id))

const loading = ref(false)
const reportList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    name: '',
    type: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建报表')
const form = reactive({
    report_id: '',
    dashboard_id: dashboardId.value,
    name: '',
    type: '',
    data_source: '',
    query: '',
    config: '',
})

// 获取报表列表
const getReportList = async () => {
    loading.value = true
    try {
        const response = await reportApi.getList({
            dashboard_id: dashboardId.value,
            page: page.value,
            limit: limit.value,
            name: searchForm.name,
            type: searchForm.type,
        })
        reportList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取报表列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getReportList()
}

// 重置
const resetForm = () => {
    searchForm.name = ''
    searchForm.type = ''
    page.value = 1
    getReportList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getReportList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getReportList()
}

// 新建报表
const handleAdd = () => {
    dialogTitle.value = '新建报表'
    form.report_id = ''
    form.dashboard_id = dashboardId.value
    form.name = ''
    form.type = ''
    form.data_source = ''
    form.query = ''
    form.config = ''
    dialogVisible.value = true
}

// 编辑报表
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑报表'
    form.report_id = row.report_id
    form.dashboard_id = row.dashboard_id
    form.name = row.name
    form.type = row.type
    form.data_source = row.data_source
    form.query = row.query
    form.config = JSON.stringify(row.config)
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.report_id) {
            // 更新报表
            await reportApi.update({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('更新报表成功')
        } else {
            // 创建报表
            await reportApi.create({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('创建报表成功')
        }
        dialogVisible.value = false
        getReportList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除报表
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该报表吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await reportApi.delete(id)
        ElMessage.success('删除报表成功')
        getReportList()
    } catch (error) {
        // 取消删除
    }
}

// 图表管理
const handleCharts = (reportId: number) => {
    // 跳转到图表管理页面
    window.location.href = `/admin/analytics/chart/index/${reportId}`
}

onMounted(() => {
    getReportList()
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
