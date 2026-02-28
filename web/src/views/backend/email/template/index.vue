<template>
    <div class="email-template">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>邮件模板管理</span>
                    <el-button type="primary" @click="handleAdd">添加模板</el-button>
                </div>
            </template>

            <el-table :data="templateList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="模板名称" />
                <el-table-column prop="code" label="模板代码" />
                <el-table-column prop="subject" label="邮件主题" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status" type="success">启用</el-tag>
                        <el-tag v-else type="danger">禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleEdit(row)">编辑</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新增/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="800px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="模板名称">
                    <el-input v-model="form.name" placeholder="请输入模板名称" />
                </el-form-item>
                <el-form-item label="模板代码">
                    <el-input v-model="form.code" placeholder="请输入模板代码" />
                </el-form-item>
                <el-form-item label="邮件主题">
                    <el-input v-model="form.subject" placeholder="请输入邮件主题" />
                </el-form-item>
                <el-form-item label="邮件内容">
                    <el-input v-model="form.content" type="textarea" placeholder="请输入邮件内容，支持变量 {variable}" :rows="10" />
                </el-form-item>
                <el-form-item label="变量定义">
                    <el-input v-model="form.variables" type="textarea" placeholder="请输入变量定义，JSON格式" :rows="3" />
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch v-model="form.status" />
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
import { ref, onMounted } from 'vue'
import { emailTemplateApi } from '/@/api/backend/email'
import { ElMessage } from 'element-plus'

// 类型定义
interface EmailTemplate {
    id: number
    name: string
    code: string
    subject: string
    content: string
    variables: string
    status: boolean
}

// 数据
const templateList = ref<EmailTemplate[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('添加模板')
const form = ref({
    name: '',
    code: '',
    subject: '',
    content: '',
    variables: '{}',
    status: true,
})
const currentId = ref(0)

// 生命周期
onMounted(() => {
    fetchTemplateList()
})

// 方法
const fetchTemplateList = async () => {
    try {
        const response = await emailTemplateApi.getList()
        if (response.code === 0) {
            templateList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取模板列表失败')
    }
}

const handleAdd = () => {
    dialogTitle.value = '添加模板'
    form.value = {
        name: '',
        code: '',
        subject: '',
        content: '',
        variables: '{}',
        status: true,
    }
    currentId.value = 0
    dialogVisible.value = true
}

const handleEdit = (row: EmailTemplate) => {
    dialogTitle.value = '编辑模板'
    form.value = { ...row }
    currentId.value = row.id
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        let response
        if (currentId.value) {
            response = await emailTemplateApi.update(currentId.value, form.value)
        } else {
            response = await emailTemplateApi.create(form.value)
        }
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchTemplateList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await emailTemplateApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchTemplateList()
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
