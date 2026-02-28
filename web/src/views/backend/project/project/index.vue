<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>项目管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建项目
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="项目名称">
                    <el-input v-model="searchForm.title" placeholder="请输入项目名称" />
                </el-form-item>
                <el-form-item label="项目状态">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="待开始" value="0" />
                        <el-option label="进行中" value="1" />
                        <el-option label="已完成" value="2" />
                        <el-option label="已暂停" value="3" />
                        <el-option label="已取消" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="projectList" style="width: 100%">
                <el-table-column prop="project_id" label="项目ID" width="80" />
                <el-table-column prop="title" label="项目名称" />
                <el-table-column prop="status_text" label="状态" width="100" />
                <el-table-column prop="completion_rate" label="完成率" width="100">
                    <template #default="scope">
                        <el-progress :percentage="scope.row.completion_rate" :color="getProgressColor(scope.row.completion_rate)" />
                    </template>
                </el-table-column>
                <el-table-column prop="start_date" label="开始日期" width="150" />
                <el-table-column prop="end_date" label="结束日期" width="150" />
                <el-table-column prop="creator.username" label="创建人" width="120" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="200">
                    <template #default="scope">
                        <el-button type="primary" size="small" @click="handleTasks(scope.row.project_id)"> 任务管理 </el-button>
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.project_id)"> 删除 </el-button>
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

        <!-- 新建/编辑项目对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="项目名称" required>
                    <el-input v-model="form.title" placeholder="请输入项目名称" />
                </el-form-item>
                <el-form-item label="项目描述">
                    <el-input v-model="form.description" type="textarea" :rows="4" placeholder="请输入项目描述" />
                </el-form-item>
                <el-form-item label="开始日期" required>
                    <el-date-picker v-model="form.start_date" type="date" placeholder="选择开始日期" style="width: 100%" />
                </el-form-item>
                <el-form-item label="结束日期" required>
                    <el-date-picker v-model="form.end_date" type="date" placeholder="选择结束日期" style="width: 100%" />
                </el-form-item>
                <el-form-item label="项目状态" required>
                    <el-select v-model="form.status" placeholder="请选择状态">
                        <el-option label="待开始" value="0" />
                        <el-option label="进行中" value="1" />
                        <el-option label="已完成" value="2" />
                        <el-option label="已暂停" value="3" />
                        <el-option label="已取消" value="4" />
                    </el-select>
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
import { projectApi } from '/@/api/backend/project'

const loading = ref(false)
const projectList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    title: '',
    status: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建项目')
const form = reactive({
    project_id: '',
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    status: 0,
})

// 获取项目列表
const getProjectList = async () => {
    loading.value = true
    try {
        const response = await projectApi.getList({
            page: page.value,
            limit: limit.value,
            title: searchForm.title,
            status: searchForm.status,
        })
        projectList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取项目列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getProjectList()
}

// 重置
const resetForm = () => {
    searchForm.title = ''
    searchForm.status = ''
    page.value = 1
    getProjectList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getProjectList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getProjectList()
}

// 新建项目
const handleAdd = () => {
    dialogTitle.value = '新建项目'
    form.project_id = ''
    form.title = ''
    form.description = ''
    form.start_date = ''
    form.end_date = ''
    form.status = 0
    dialogVisible.value = true
}

// 编辑项目
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑项目'
    form.project_id = row.project_id
    form.title = row.title
    form.description = row.description
    form.start_date = row.start_date
    form.end_date = row.end_date
    form.status = row.status
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.project_id) {
            // 更新项目
            await projectApi.update(form)
            ElMessage.success('更新项目成功')
        } else {
            // 创建项目
            await projectApi.create(form)
            ElMessage.success('创建项目成功')
        }
        dialogVisible.value = false
        getProjectList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除项目
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该项目吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await projectApi.delete(id)
        ElMessage.success('删除项目成功')
        getProjectList()
    } catch (error) {
        // 取消删除
    }
}

// 任务管理
const handleTasks = (projectId: number) => {
    // 跳转到任务管理页面
    window.location.href = `/admin/project/task/index/${projectId}`
}

// 获取进度条颜色
const getProgressColor = (percentage: number) => {
    if (percentage < 30) {
        return '#F56C6C'
    } else if (percentage < 70) {
        return '#E6A23C'
    } else {
        return '#67C23A'
    }
}

onMounted(() => {
    getProjectList()
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
