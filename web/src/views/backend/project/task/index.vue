<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>任务管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建任务
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="任务名称">
                    <el-input v-model="searchForm.title" placeholder="请输入任务名称" />
                </el-form-item>
                <el-form-item label="任务状态">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="待开始" value="0" />
                        <el-option label="进行中" value="1" />
                        <el-option label="已完成" value="2" />
                        <el-option label="已暂停" value="3" />
                        <el-option label="已取消" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="优先级">
                    <el-select v-model="searchForm.priority" placeholder="请选择优先级">
                        <el-option label="低" value="0" />
                        <el-option label="中" value="1" />
                        <el-option label="高" value="2" />
                        <el-option label="紧急" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="taskList" style="width: 100%">
                <el-table-column prop="task_id" label="任务ID" width="80" />
                <el-table-column prop="title" label="任务名称" />
                <el-table-column prop="status_text" label="状态" width="100" />
                <el-table-column prop="priority_text" label="优先级" width="80">
                    <template #default="scope">
                        <el-tag :type="getPriorityType(scope.row.priority)">{{ scope.row.priority_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="assignee.username" label="负责人" width="120" />
                <el-table-column prop="start_date" label="开始日期" width="150" />
                <el-table-column prop="end_date" label="结束日期" width="150" />
                <el-table-column prop="creator.username" label="创建人" width="120" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="250">
                    <template #default="scope">
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="primary" size="small" @click="handleUpdateStatus(scope.row)"> 更新状态 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.task_id)"> 删除 </el-button>
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

        <!-- 新建/编辑任务对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="任务名称" required>
                    <el-input v-model="form.title" placeholder="请输入任务名称" />
                </el-form-item>
                <el-form-item label="任务描述">
                    <el-input v-model="form.description" type="textarea" :rows="4" placeholder="请输入任务描述" />
                </el-form-item>
                <el-form-item label="负责人" required>
                    <el-select v-model="form.assignee_id" placeholder="请选择负责人">
                        <el-option v-for="user in users" :key="user.id" :label="user.username" :value="user.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="优先级" required>
                    <el-select v-model="form.priority" placeholder="请选择优先级">
                        <el-option label="低" value="0" />
                        <el-option label="中" value="1" />
                        <el-option label="高" value="2" />
                        <el-option label="紧急" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" required>
                    <el-select v-model="form.status" placeholder="请选择状态">
                        <el-option label="待开始" value="0" />
                        <el-option label="进行中" value="1" />
                        <el-option label="已完成" value="2" />
                        <el-option label="已暂停" value="3" />
                        <el-option label="已取消" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="开始日期" required>
                    <el-date-picker v-model="form.start_date" type="date" placeholder="选择开始日期" style="width: 100%" />
                </el-form-item>
                <el-form-item label="结束日期" required>
                    <el-date-picker v-model="form.end_date" type="date" placeholder="选择结束日期" style="width: 100%" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 更新状态对话框 -->
        <el-dialog v-model="statusDialogVisible" title="更新任务状态" width="300px">
            <el-form :model="statusForm" label-width="80px">
                <el-form-item label="任务状态" required>
                    <el-select v-model="statusForm.status" placeholder="请选择状态">
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
                    <el-button @click="statusDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleStatusSubmit">确定</el-button>
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
import { taskApi } from '/@/api/backend/project'

const route = useRoute()
const projectId = ref(Number(route.params.id))

const loading = ref(false)
const taskList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)
const users = ref<any[]>([])

const searchForm = reactive({
    title: '',
    status: '',
    priority: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建任务')
const form = reactive({
    task_id: '',
    project_id: projectId.value,
    title: '',
    description: '',
    assignee_id: '',
    priority: 0,
    status: 0,
    start_date: '',
    end_date: '',
})

const statusDialogVisible = ref(false)
const statusForm = reactive({
    task_id: '',
    status: 0,
})

// 获取任务列表
const getTaskList = async () => {
    loading.value = true
    try {
        const response = await taskApi.getList({
            project_id: projectId.value,
            page: page.value,
            limit: limit.value,
            title: searchForm.title,
            status: searchForm.status,
            priority: searchForm.priority,
        })
        taskList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取任务列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getTaskList()
}

// 重置
const resetForm = () => {
    searchForm.title = ''
    searchForm.status = ''
    searchForm.priority = ''
    page.value = 1
    getTaskList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getTaskList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getTaskList()
}

// 新建任务
const handleAdd = () => {
    dialogTitle.value = '新建任务'
    form.task_id = ''
    form.project_id = projectId.value
    form.title = ''
    form.description = ''
    form.assignee_id = ''
    form.priority = 0
    form.status = 0
    form.start_date = ''
    form.end_date = ''
    dialogVisible.value = true
}

// 编辑任务
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑任务'
    form.task_id = row.task_id
    form.project_id = row.project_id
    form.title = row.title
    form.description = row.description
    form.assignee_id = row.assignee_id
    form.priority = row.priority
    form.status = row.status
    form.start_date = row.start_date
    form.end_date = row.end_date
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.task_id) {
            // 更新任务
            await taskApi.update(form)
            ElMessage.success('更新任务成功')
        } else {
            // 创建任务
            await taskApi.create(form)
            ElMessage.success('创建任务成功')
        }
        dialogVisible.value = false
        getTaskList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除任务
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该任务吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await taskApi.delete(id)
        ElMessage.success('删除任务成功')
        getTaskList()
    } catch (error) {
        // 取消删除
    }
}

// 更新状态
const handleUpdateStatus = (row: any) => {
    statusForm.task_id = row.task_id
    statusForm.status = row.status
    statusDialogVisible.value = true
}

// 提交状态更新
const handleStatusSubmit = async () => {
    try {
        await taskApi.updateStatus(statusForm)
        ElMessage.success('更新状态成功')
        statusDialogVisible.value = false
        getTaskList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 获取优先级标签类型
const getPriorityType = (priority: number) => {
    switch (priority) {
        case 0:
            return 'info'
        case 1:
            return 'warning'
        case 2:
            return 'danger'
        case 3:
            return 'danger'
        default:
            return 'info'
    }
}

// 获取用户列表
const getUsers = async () => {
    try {
        // 这里应该调用获取用户列表的API
        // 暂时模拟数据
        users.value = [
            { id: 1, username: 'admin' },
            { id: 2, username: 'user1' },
            { id: 3, username: 'user2' },
        ]
    } catch (error) {
        ElMessage.error('获取用户列表失败')
    }
}

onMounted(() => {
    getUsers()
    getTaskList()
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
