<template>
    <div class="social-message">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>社交媒体消息管理</span>
                    <el-button type="primary" @click="handleAdd">创建消息</el-button>
                    <el-button type="info" @click="handleGetUnreadCount">未读消息</el-button>
                </div>
            </template>

            <el-table :data="messageList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="message_type" label="消息类型" />
                <el-table-column prop="content" label="消息内容" show-overflow-tooltip />
                <el-table-column prop="sender" label="发送者" />
                <el-table-column prop="receiver" label="接收者" />
                <el-table-column prop="config.platform" label="平台" width="120" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="info">未读</el-tag>
                        <el-tag v-else type="success">已读</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建时间" width="180">
                    <template #default="{ row }">
                        {{ row.create_time ? new Date(row.create_time * 1000).toLocaleString() : '-' }}
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleRead(row.id)">查看</el-button>
                        <el-button v-if="row.status === 0" size="small" @click="handleMarkAsRead(row.id)">标记已读</el-button>
                        <el-button v-else size="small" @click="handleMarkAsUnread(row.id)">标记未读</el-button>
                        <el-button size="small" @click="handleSend(row.id)">发送</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新增消息对话框 -->
        <el-dialog v-model="dialogVisible" title="创建消息" width="600px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="配置ID">
                    <el-input v-model.number="form.config_id" placeholder="请输入配置ID" type="number" />
                </el-form-item>
                <el-form-item label="消息类型">
                    <el-input v-model="form.message_type" placeholder="请输入消息类型" />
                </el-form-item>
                <el-form-item label="消息内容">
                    <el-input v-model="form.content" type="textarea" placeholder="请输入消息内容" :rows="3" />
                </el-form-item>
                <el-form-item label="发送者">
                    <el-input v-model="form.sender" placeholder="请输入发送者" />
                </el-form-item>
                <el-form-item label="接收者">
                    <el-input v-model="form.receiver" placeholder="请输入接收者" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 消息详情对话框 -->
        <el-dialog v-model="detailDialogVisible" title="消息详情" width="600px">
            <el-form :model="messageDetail" label-width="120px">
                <el-form-item label="消息类型">
                    <el-input v-model="messageDetail.message_type" disabled />
                </el-form-item>
                <el-form-item label="消息内容">
                    <el-input v-model="messageDetail.content" type="textarea" disabled :rows="3" />
                </el-form-item>
                <el-form-item label="发送者">
                    <el-input v-model="messageDetail.sender" disabled />
                </el-form-item>
                <el-form-item label="接收者">
                    <el-input v-model="messageDetail.receiver" disabled />
                </el-form-item>
                <el-form-item label="平台">
                    <el-input :value="messageDetail.config?.platform" disabled />
                </el-form-item>
                <el-form-item label="状态">
                    <el-input v-model="messageDetail.statusText" disabled />
                </el-form-item>
                <el-form-item label="创建时间">
                    <el-input v-model="messageDetail.createTimeText" disabled />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="detailDialogVisible = false">关闭</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { socialMessageApi } from '/@/api/backend/social'
import { ElMessage } from 'element-plus'

// 类型定义
interface Message {
    id: number
    config_id: number
    message_type: string
    content: string
    sender: string
    receiver: string
    status: number
    create_time?: number
    config?: {
        platform: string
    }
}

interface MessageDetail extends Message {
    statusText: string
    createTimeText: string
}

// 数据
const messageList = ref<Message[]>([])
const dialogVisible = ref(false)
const detailDialogVisible = ref(false)
const form = ref({
    config_id: 0,
    message_type: '',
    content: '',
    sender: '',
    receiver: '',
})
const messageDetail = ref<MessageDetail>({
    id: 0,
    config_id: 0,
    message_type: '',
    content: '',
    sender: '',
    receiver: '',
    status: 0,
    statusText: '',
    createTimeText: '',
    config: {
        platform: '',
    },
})

// 生命周期
onMounted(() => {
    fetchMessageList()
})

// 方法
const fetchMessageList = async () => {
    try {
        const response = await socialMessageApi.getList()
        if (response.code === 0) {
            messageList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取消息列表失败')
    }
}

const handleAdd = () => {
    form.value = {
        config_id: 0,
        message_type: '',
        content: '',
        sender: '',
        receiver: '',
    }
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        const response = await socialMessageApi.create(form.value)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchMessageList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('创建失败')
    }
}

const handleRead = async (id: number) => {
    try {
        const response = await socialMessageApi.getOne(id)
        if (response.code === 0) {
            const data = response.data
            messageDetail.value = {
                ...data,
                statusText: data.status === 0 ? '未读' : '已读',
                createTimeText: data.create_time ? new Date(data.create_time * 1000).toLocaleString() : '-',
                config: data.config || { platform: '' },
            }
            detailDialogVisible.value = true
            fetchMessageList() // 刷新列表以更新状态
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('获取消息详情失败')
    }
}

const handleMarkAsRead = async (id: number) => {
    try {
        const response = await socialMessageApi.markAsRead(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchMessageList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('标记失败')
    }
}

const handleMarkAsUnread = async (id: number) => {
    try {
        const response = await socialMessageApi.markAsUnread(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchMessageList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('标记失败')
    }
}

const handleSend = async (id: number) => {
    try {
        const response = await socialMessageApi.send(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('发送失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await socialMessageApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchMessageList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}

const handleGetUnreadCount = async () => {
    try {
        const response = await socialMessageApi.getUnreadCount()
        if (response.code === 0) {
            ElMessage.info(`未读消息数量: ${response.data.count}`)
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('获取未读消息数量失败')
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
