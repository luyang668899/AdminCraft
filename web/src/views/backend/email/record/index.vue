<template>
    <div class="email-record">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>邮件发送记录</span>
                    <el-button type="primary" @click="handleAdd">发送邮件</el-button>
                    <el-button type="success" @click="handleBatchSend">批量发送</el-button>
                </div>
            </template>

            <el-table :data="recordList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="to_email" label="收件人邮箱" />
                <el-table-column prop="subject" label="邮件主题" />
                <el-table-column prop="config.name" label="配置名称" width="120" />
                <el-table-column prop="template.name" label="模板名称" width="120" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="info">待发送</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">发送成功</el-tag>
                        <el-tag v-else type="danger">发送失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="send_time" label="发送时间" width="180">
                    <template #default="{ row }">
                        {{ row.send_time ? new Date(row.send_time * 1000).toLocaleString() : '-' }}
                    </template>
                </el-table-column>
                <el-table-column prop="error_msg" label="错误信息" show-overflow-tooltip />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleResend(row.id)">重新发送</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 发送邮件对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="收件人邮箱">
                    <el-input v-model="form.to_email" placeholder="请输入收件人邮箱" />
                </el-form-item>
                <el-form-item label="邮件主题">
                    <el-input v-model="form.subject" placeholder="请输入邮件主题" />
                </el-form-item>
                <el-form-item label="邮件内容">
                    <el-input v-model="form.content" type="textarea" placeholder="请输入邮件内容" :rows="5" />
                </el-form-item>
                <el-form-item label="配置ID">
                    <el-input v-model.number="form.config_id" placeholder="请输入配置ID" type="number" />
                </el-form-item>
                <el-form-item label="模板ID">
                    <el-input v-model.number="form.template_id" placeholder="请输入模板ID" type="number" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSubmit">发送</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 批量发送对话框 -->
        <el-dialog v-model="batchDialogVisible" title="批量发送邮件" width="600px">
            <el-form :model="batchForm" label-width="120px">
                <el-form-item label="收件人邮箱">
                    <el-input v-model="batchForm.emails" type="textarea" placeholder="请输入多个邮箱，用逗号分隔" :rows="3" />
                </el-form-item>
                <el-form-item label="模板ID">
                    <el-input v-model.number="batchForm.template_id" placeholder="请输入模板ID" type="number" />
                </el-form-item>
                <el-form-item label="变量值">
                    <el-input v-model="batchForm.variables" type="textarea" placeholder="请输入变量值，JSON格式" :rows="3" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="batchDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleBatchSubmit">批量发送</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { emailRecordApi } from '/@/api/backend/email'
import { ElMessage } from 'element-plus'

// 类型定义
interface EmailRecord {
    id: number
    to_email: string
    subject: string
    content?: string
    config?: {
        name: string
    }
    template?: {
        name: string
    }
    status: number
    send_time?: number
    error_msg?: string
}

// 数据
const recordList = ref<EmailRecord[]>([])
const dialogVisible = ref(false)
const batchDialogVisible = ref(false)
const dialogTitle = ref('发送邮件')
const form = ref({
    to_email: '',
    subject: '',
    content: '',
    config_id: 0,
    template_id: 0,
})
const batchForm = ref({
    emails: '',
    template_id: 0,
    variables: '{}',
})

// 生命周期
onMounted(() => {
    fetchRecordList()
})

// 方法
const fetchRecordList = async () => {
    try {
        const response = await emailRecordApi.getList()
        if (response.code === 0) {
            recordList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取记录列表失败')
    }
}

const handleAdd = () => {
    dialogTitle.value = '发送邮件'
    form.value = {
        to_email: '',
        subject: '',
        content: '',
        config_id: 0,
        template_id: 0,
    }
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        const response = await emailRecordApi.create(form.value)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchRecordList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('发送失败')
    }
}

const handleResend = async (id: number) => {
    try {
        const response = await emailRecordApi.resend(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchRecordList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('重新发送失败')
    }
}

const handleBatchSend = () => {
    batchForm.value = {
        emails: '',
        template_id: 0,
        variables: '{}',
    }
    batchDialogVisible.value = true
}

const handleBatchSubmit = async () => {
    try {
        const response = await emailRecordApi.batchSend(batchForm.value)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            batchDialogVisible.value = false
            fetchRecordList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('批量发送失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await emailRecordApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchRecordList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}
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
