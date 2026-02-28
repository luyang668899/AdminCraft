<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>短信记录管理</span>
                    <el-button type="primary" @click="handleSend">
                        <el-icon><Plus /></el-icon>
                        发送短信
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="手机号码">
                    <el-input v-model="searchForm.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="短信类型">
                    <el-select v-model="searchForm.type" placeholder="请选择短信类型">
                        <el-option label="验证码" value="verification" />
                        <el-option label="通知" value="notification" />
                        <el-option label="营销" value="marketing" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="待发送" value="0" />
                        <el-option label="发送成功" value="1" />
                        <el-option label="发送失败" value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="recordList" style="width: 100%">
                <el-table-column prop="record_id" label="记录ID" width="80" />
                <el-table-column prop="mobile" label="手机号码" width="120" />
                <el-table-column prop="type" label="短信类型" width="120" />
                <el-table-column prop="content" label="短信内容" />
                <el-table-column prop="status_text" label="状态" width="120" />
                <el-table-column prop="send_time" label="发送时间" width="180" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="150">
                    <template #default="scope">
                        <el-button size="small" @click="handleView(scope.row.record_id)"> 查看 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.record_id)"> 删除 </el-button>
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

        <!-- 发送短信对话框 -->
        <el-dialog v-model="sendDialogVisible" title="发送短信" width="600px">
            <el-form :model="sendForm" label-width="100px">
                <el-form-item label="短信配置" required>
                    <el-select v-model="sendForm.config_id" placeholder="请选择短信配置">
                        <el-option v-for="config in configList" :key="config.config_id" :label="config.name" :value="config.config_id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="手机号码" required>
                    <el-input v-model="sendForm.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="短信类型" required>
                    <el-select v-model="sendForm.type" placeholder="请选择短信类型">
                        <el-option label="验证码" value="verification" />
                        <el-option label="通知" value="notification" />
                        <el-option label="营销" value="marketing" />
                    </el-select>
                </el-form-item>
                <el-form-item label="短信内容" required>
                    <el-input v-model="sendForm.content" type="textarea" :rows="4" placeholder="请输入短信内容" />
                </el-form-item>
                <el-form-item label="模板参数">
                    <el-input v-model="sendForm.template_params" type="textarea" :rows="4" placeholder="请输入模板参数（JSON格式）" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="sendDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSendSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 查看短信记录对话框 -->
        <el-dialog v-model="viewDialogVisible" title="查看短信记录" width="800px">
            <el-form :model="recordDetail" label-width="100px">
                <el-form-item label="记录ID">
                    <el-input v-model="recordDetail.record_id" disabled />
                </el-form-item>
                <el-form-item label="手机号码">
                    <el-input v-model="recordDetail.mobile" disabled />
                </el-form-item>
                <el-form-item label="短信类型">
                    <el-input v-model="recordDetail.type" disabled />
                </el-form-item>
                <el-form-item label="短信内容">
                    <el-input v-model="recordDetail.content" type="textarea" :rows="4" disabled />
                </el-form-item>
                <el-form-item label="模板参数">
                    <el-input v-model="recordDetail.template_params" type="textarea" :rows="4" disabled />
                </el-form-item>
                <el-form-item label="状态">
                    <el-input v-model="recordDetail.status_text" disabled />
                </el-form-item>
                <el-form-item label="发送时间">
                    <el-input v-model="recordDetail.send_time" disabled />
                </el-form-item>
                <el-form-item label="错误信息">
                    <el-input v-model="recordDetail.error_message" type="textarea" :rows="4" disabled />
                </el-form-item>
                <el-form-item label="短信配置">
                    <el-input v-model="recordDetail.config_name" disabled />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="viewDialogVisible = false">关闭</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { smsRecordApi, smsConfigApi } from '/@/api/backend/sms'

const loading = ref(false)
const recordList = ref<any[]>([])
const configList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    mobile: '',
    type: '',
    status: '',
})

const sendDialogVisible = ref(false)
const sendForm = reactive({
    config_id: '',
    mobile: '',
    type: '',
    content: '',
    template_params: '',
})

const viewDialogVisible = ref(false)
const recordDetail = reactive({
    record_id: 0,
    mobile: '',
    type: '',
    content: '',
    template_params: '',
    status_text: '',
    send_time: '',
    error_message: '',
    config_name: '',
})

// 获取短信记录列表
const getRecordList = async () => {
    loading.value = true
    try {
        const response = await smsRecordApi.getList({
            page: page.value,
            limit: limit.value,
            mobile: searchForm.mobile,
            type: searchForm.type,
            status: searchForm.status,
        })
        recordList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取短信记录列表失败')
    } finally {
        loading.value = false
    }
}

// 获取短信配置列表
const getConfigList = async () => {
    try {
        const response = await smsConfigApi.getList({ limit: 100 })
        configList.value = response.data.data
    } catch (error) {
        ElMessage.error('获取短信配置列表失败')
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getRecordList()
}

// 重置
const resetForm = () => {
    searchForm.mobile = ''
    searchForm.type = ''
    searchForm.status = ''
    page.value = 1
    getRecordList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getRecordList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getRecordList()
}

// 发送短信
const handleSend = () => {
    sendForm.config_id = ''
    sendForm.mobile = ''
    sendForm.type = ''
    sendForm.content = ''
    sendForm.template_params = ''
    sendDialogVisible.value = true
}

// 提交发送短信
const handleSendSubmit = async () => {
    try {
        await smsRecordApi.send({
            ...sendForm,
            template_params: sendForm.template_params ? JSON.parse(sendForm.template_params) : {},
        })
        ElMessage.success('短信发送成功')
        sendDialogVisible.value = false
        getRecordList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 查看短信记录
const handleView = async (id: number) => {
    // 这里应该调用API获取详细信息，暂时模拟数据
    recordDetail.record_id = id
    recordDetail.mobile = '13800138000'
    recordDetail.type = '验证码'
    recordDetail.content = '您的验证码是：123456，有效期5分钟'
    recordDetail.template_params = JSON.stringify({ code: '123456' })
    recordDetail.status_text = '发送成功'
    recordDetail.send_time = new Date().toISOString()
    recordDetail.error_message = ''
    recordDetail.config_name = '阿里云短信配置'
    viewDialogVisible.value = true
}

// 删除短信记录
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该短信记录吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await smsRecordApi.delete(id)
        ElMessage.success('删除短信记录成功')
        getRecordList()
    } catch (error) {
        // 取消删除
    }
}

onMounted(() => {
    getConfigList()
    getRecordList()
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
