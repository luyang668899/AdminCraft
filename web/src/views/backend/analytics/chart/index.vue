<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>图表管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建图表
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="图表名称">
                    <el-input v-model="searchForm.name" placeholder="请输入图表名称" />
                </el-form-item>
                <el-form-item label="图表类型">
                    <el-select v-model="searchForm.type" placeholder="请选择图表类型">
                        <el-option label="折线图" value="line" />
                        <el-option label="柱状图" value="bar" />
                        <el-option label="饼图" value="pie" />
                        <el-option label="散点图" value="scatter" />
                        <el-option label="雷达图" value="radar" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="chartList" style="width: 100%">
                <el-table-column prop="chart_id" label="图表ID" width="80" />
                <el-table-column prop="name" label="图表名称" />
                <el-table-column prop="type" label="图表类型" width="100" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="150">
                    <template #default="scope">
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.chart_id)"> 删除 </el-button>
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

        <!-- 新建/编辑图表对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="图表名称" required>
                    <el-input v-model="form.name" placeholder="请输入图表名称" />
                </el-form-item>
                <el-form-item label="图表类型" required>
                    <el-select v-model="form.type" placeholder="请选择图表类型">
                        <el-option label="折线图" value="line" />
                        <el-option label="柱状图" value="bar" />
                        <el-option label="饼图" value="pie" />
                        <el-option label="散点图" value="scatter" />
                        <el-option label="雷达图" value="radar" />
                    </el-select>
                </el-form-item>
                <el-form-item label="配置">
                    <el-input v-model="form.config" type="textarea" :rows="8" placeholder="请输入图表配置（JSON格式）" />
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
import { chartApi } from '/@/api/backend/analytics'

const route = useRoute()
const reportId = ref(Number(route.params.id))

const loading = ref(false)
const chartList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    name: '',
    type: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建图表')
const form = reactive({
    chart_id: '',
    report_id: reportId.value,
    name: '',
    type: '',
    config: '',
})

// 获取图表列表
const getChartList = async () => {
    loading.value = true
    try {
        const response = await chartApi.getList({
            report_id: reportId.value,
            page: page.value,
            limit: limit.value,
            name: searchForm.name,
            type: searchForm.type,
        })
        chartList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取图表列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getChartList()
}

// 重置
const resetForm = () => {
    searchForm.name = ''
    searchForm.type = ''
    page.value = 1
    getChartList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getChartList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getChartList()
}

// 新建图表
const handleAdd = () => {
    dialogTitle.value = '新建图表'
    form.chart_id = ''
    form.report_id = reportId.value
    form.name = ''
    form.type = ''
    form.config = ''
    dialogVisible.value = true
}

// 编辑图表
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑图表'
    form.chart_id = row.chart_id
    form.report_id = row.report_id
    form.name = row.name
    form.type = row.type
    form.config = JSON.stringify(row.config)
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.chart_id) {
            // 更新图表
            await chartApi.update({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('更新图表成功')
        } else {
            // 创建图表
            await chartApi.create({
                ...form,
                config: JSON.parse(form.config),
            })
            ElMessage.success('创建图表成功')
        }
        dialogVisible.value = false
        getChartList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除图表
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该图表吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await chartApi.delete(id)
        ElMessage.success('删除图表成功')
        getChartList()
    } catch (error) {
        // 取消删除
    }
}

onMounted(() => {
    getChartList()
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
