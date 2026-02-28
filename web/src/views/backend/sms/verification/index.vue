<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>短信验证码管理</span>
                    <el-button type="primary" @click="handleSendCode">
                        <el-icon><Plus /></el-icon>
                        发送验证码
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="手机号码">
                    <el-input v-model="searchForm.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="验证码类型">
                    <el-select v-model="searchForm.type" placeholder="请选择验证码类型">
                        <el-option label="登录" value="login" />
                        <el-option label="注册" value="register" />
                        <el-option label="重置密码" value="reset_password" />
                        <el-option label="修改手机" value="change_mobile" />
                    </el-select>
                </el-form-item>
                <el-form-item label="使用状态">
                    <el-select v-model="searchForm.is_used" placeholder="请选择使用状态">
                        <el-option label="未使用" value="0" />
                        <el-option label="已使用" value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="verificationList" style="width: 100%">
                <el-table-column prop="verification_id" label="验证码ID" width="80" />
                <el-table-column prop="mobile" label="手机号码" width="120" />
                <el-table-column prop="code" label="验证码" width="100" />
                <el-table-column prop="type" label="验证码类型" width="120" />
                <el-table-column prop="expire_time" label="过期时间" width="180" />
                <el-table-column prop="is_used_text" label="使用状态" width="100" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="150">
                    <template #default="scope">
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.verification_id)"> 删除 </el-button>
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

        <!-- 发送验证码对话框 -->
        <el-dialog v-model="sendDialogVisible" title="发送验证码" width="500px">
            <el-form :model="sendForm" label-width="100px">
                <el-form-item label="手机号码" required>
                    <el-input v-model="sendForm.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="验证码类型" required>
                    <el-select v-model="sendForm.type" placeholder="请选择验证码类型">
                        <el-option label="登录" value="login" />
                        <el-option label="注册" value="register" />
                        <el-option label="重置密码" value="reset_password" />
                        <el-option label="修改手机" value="change_mobile" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="sendDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSendCodeSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 验证验证码对话框 -->
        <el-dialog v-model="verifyDialogVisible" title="验证验证码" width="500px">
            <el-form :model="verifyForm" label-width="100px">
                <el-form-item label="手机号码" required>
                    <el-input v-model="verifyForm.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="验证码" required>
                    <el-input v-model="verifyForm.code" placeholder="请输入验证码" />
                </el-form-item>
                <el-form-item label="验证码类型" required>
                    <el-select v-model="verifyForm.type" placeholder="请选择验证码类型">
                        <el-option label="登录" value="login" />
                        <el-option label="注册" value="register" />
                        <el-option label="重置密码" value="reset_password" />
                        <el-option label="修改手机" value="change_mobile" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="verifyDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleVerifyCodeSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { smsVerificationApi } from '/@/api/backend/sms'

const loading = ref(false)
const verificationList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    mobile: '',
    type: '',
    is_used: '',
})

const sendDialogVisible = ref(false)
const sendForm = reactive({
    mobile: '',
    type: '',
})

const verifyDialogVisible = ref(false)
const verifyForm = reactive({
    mobile: '',
    code: '',
    type: '',
})

// 获取短信验证码列表
const getVerificationList = async () => {
    loading.value = true
    try {
        const response = await smsVerificationApi.getList({
            page: page.value,
            limit: limit.value,
            mobile: searchForm.mobile,
            type: searchForm.type,
            is_used: searchForm.is_used,
        })
        verificationList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取短信验证码列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getVerificationList()
}

// 重置
const resetForm = () => {
    searchForm.mobile = ''
    searchForm.type = ''
    searchForm.is_used = ''
    page.value = 1
    getVerificationList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getVerificationList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getVerificationList()
}

// 发送验证码
const handleSendCode = () => {
    sendForm.mobile = ''
    sendForm.type = ''
    sendDialogVisible.value = true
}

// 提交发送验证码
const handleSendCodeSubmit = async () => {
    try {
        await smsVerificationApi.sendCode(sendForm)
        ElMessage.success('验证码发送成功')
        sendDialogVisible.value = false
        getVerificationList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 验证验证码
const handleVerifyCode = () => {
    verifyForm.mobile = ''
    verifyForm.code = ''
    verifyForm.type = ''
    verifyDialogVisible.value = true
}

// 提交验证验证码
const handleVerifyCodeSubmit = async () => {
    try {
        await smsVerificationApi.verifyCode(verifyForm)
        ElMessage.success('验证码验证成功')
        verifyDialogVisible.value = false
        getVerificationList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除短信验证码
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该短信验证码吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await smsVerificationApi.delete(id)
        ElMessage.success('删除短信验证码成功')
        getVerificationList()
    } catch (error) {
        // 取消删除
    }
}

onMounted(() => {
    getVerificationList()
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
