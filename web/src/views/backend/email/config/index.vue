<template>
    <div class="email-config">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>邮件配置管理</span>
                    <el-button type="primary" @click="handleAdd">添加配置</el-button>
                </div>
            </template>

            <el-table :data="configList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="配置名称" />
                <el-table-column prop="type" label="邮件类型" />
                <el-table-column prop="host" label="SMTP主机" />
                <el-table-column prop="port" label="端口" width="80" />
                <el-table-column prop="from_email" label="发件人邮箱" />
                <el-table-column prop="from_name" label="发件人名称" />
                <el-table-column prop="is_default" label="是否默认" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_default" type="success">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
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
                        <el-button v-if="!row.is_default" size="small" @click="handleSetDefault(row.id)">设为默认</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新增/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="配置名称">
                    <el-input v-model="form.name" placeholder="请输入配置名称" />
                </el-form-item>
                <el-form-item label="邮件类型">
                    <el-select v-model="form.type" placeholder="请选择邮件类型">
                        <el-option label="SMTP" value="smtp" />
                        <el-option label="API" value="api" />
                    </el-select>
                </el-form-item>
                <el-form-item label="SMTP主机">
                    <el-input v-model="form.host" placeholder="请输入SMTP主机" />
                </el-form-item>
                <el-form-item label="SMTP端口">
                    <el-input v-model.number="form.port" placeholder="请输入SMTP端口" type="number" />
                </el-form-item>
                <el-form-item label="用户名">
                    <el-input v-model="form.username" placeholder="请输入用户名" />
                </el-form-item>
                <el-form-item label="密码">
                    <el-input v-model="form.password" placeholder="请输入密码" type="password" />
                </el-form-item>
                <el-form-item label="发件人邮箱">
                    <el-input v-model="form.from_email" placeholder="请输入发件人邮箱" />
                </el-form-item>
                <el-form-item label="发件人名称">
                    <el-input v-model="form.from_name" placeholder="请输入发件人名称" />
                </el-form-item>
                <el-form-item label="是否使用SSL">
                    <el-switch v-model="form.ssl" />
                </el-form-item>
                <el-form-item label="是否默认">
                    <el-switch v-model="form.is_default" />
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
import { emailConfigApi } from '/@/api/backend/email'
import { ElMessage } from 'element-plus'

// 类型定义
interface EmailConfig {
    id: number
    name: string
    type: string
    host: string
    port: number
    username: string
    password: string
    from_email: string
    from_name: string
    ssl: boolean
    is_default: boolean
    status: boolean
}

// 数据
const configList = ref<EmailConfig[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('添加配置')
const form = ref({
    name: '',
    type: 'smtp',
    host: '',
    port: 25,
    username: '',
    password: '',
    from_email: '',
    from_name: '',
    ssl: true,
    is_default: false,
    status: true,
})
const currentId = ref(0)

// 生命周期
onMounted(() => {
    fetchConfigList()
})

// 方法
const fetchConfigList = async () => {
    try {
        const response = await emailConfigApi.getList()
        if (response.code === 0) {
            configList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取配置列表失败')
    }
}

const handleAdd = () => {
    dialogTitle.value = '添加配置'
    form.value = {
        name: '',
        type: 'smtp',
        host: '',
        port: 25,
        username: '',
        password: '',
        from_email: '',
        from_name: '',
        ssl: true,
        is_default: false,
        status: true,
    }
    currentId.value = 0
    dialogVisible.value = true
}

const handleEdit = (row: EmailConfig) => {
    dialogTitle.value = '编辑配置'
    form.value = { ...row }
    currentId.value = row.id
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        let response
        if (currentId.value) {
            response = await emailConfigApi.update(currentId.value, form.value)
        } else {
            response = await emailConfigApi.create(form.value)
        }
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await emailConfigApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}

const handleSetDefault = async (id: number) => {
    try {
        const response = await emailConfigApi.setDefault(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('设置失败')
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
