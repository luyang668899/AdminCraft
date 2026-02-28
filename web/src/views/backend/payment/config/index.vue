<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>支付配置管理</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        新建配置
                    </el-button>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="支付类型">
                    <el-select v-model="searchForm.type" placeholder="请选择支付类型">
                        <el-option label="支付宝" value="alipay" />
                        <el-option label="微信支付" value="wechat" />
                        <el-option label="PayPal" value="paypal" />
                    </el-select>
                </el-form-item>
                <el-form-item label="配置名称">
                    <el-input v-model="searchForm.name" placeholder="请输入配置名称" />
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="启用" value="1" />
                        <el-option label="禁用" value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="configList" style="width: 100%">
                <el-table-column prop="config_id" label="配置ID" width="80" />
                <el-table-column prop="type_text" label="支付类型" width="120" />
                <el-table-column prop="name" label="配置名称" />
                <el-table-column prop="app_id" label="应用ID" />
                <el-table-column prop="merchant_id" label="商户ID" />
                <el-table-column prop="currency" label="货币类型" width="100" />
                <el-table-column prop="status_text" label="状态" width="100">
                    <template #default="scope">
                        <el-switch
                            v-model="scope.row.status"
                            active-text="启用"
                            inactive-text="禁用"
                            @change="handleToggleStatus(scope.row.config_id, scope.row.status)"
                        />
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="创建时间" width="180" />
                <el-table-column label="操作" width="150">
                    <template #default="scope">
                        <el-button size="small" @click="handleEdit(scope.row)"> 编辑 </el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope.row.config_id)"> 删除 </el-button>
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

        <!-- 新建/编辑支付配置对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px">
            <el-form :model="form" label-width="100px">
                <el-form-item label="支付类型" required>
                    <el-select v-model="form.type" placeholder="请选择支付类型">
                        <el-option label="支付宝" value="alipay" />
                        <el-option label="微信支付" value="wechat" />
                        <el-option label="PayPal" value="paypal" />
                    </el-select>
                </el-form-item>
                <el-form-item label="配置名称" required>
                    <el-input v-model="form.name" placeholder="请输入配置名称" />
                </el-form-item>
                <el-form-item label="应用ID" required>
                    <el-input v-model="form.app_id" placeholder="请输入应用ID" />
                </el-form-item>
                <el-form-item label="应用密钥" required>
                    <el-input v-model="form.app_secret" placeholder="请输入应用密钥" />
                </el-form-item>
                <el-form-item label="公钥">
                    <el-input v-model="form.public_key" type="textarea" :rows="4" placeholder="请输入公钥" />
                </el-form-item>
                <el-form-item label="私钥">
                    <el-input v-model="form.private_key" type="textarea" :rows="4" placeholder="请输入私钥" />
                </el-form-item>
                <el-form-item label="商户ID" required>
                    <el-input v-model="form.merchant_id" placeholder="请输入商户ID" />
                </el-form-item>
                <el-form-item label="网关地址" required>
                    <el-input v-model="form.gateway_url" placeholder="请输入网关地址" />
                </el-form-item>
                <el-form-item label="回调地址" required>
                    <el-input v-model="form.return_url" placeholder="请输入回调地址" />
                </el-form-item>
                <el-form-item label="通知地址" required>
                    <el-input v-model="form.notify_url" placeholder="请输入通知地址" />
                </el-form-item>
                <el-form-item label="货币类型" required>
                    <el-input v-model="form.currency" placeholder="请输入货币类型" />
                </el-form-item>
                <el-form-item label="状态" required>
                    <el-switch v-model="form.status" active-text="启用" inactive-text="禁用" />
                </el-form-item>
                <el-form-item label="其他配置">
                    <el-input v-model="form.config" type="textarea" :rows="6" placeholder="请输入其他配置（JSON格式）" />
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
import { paymentConfigApi } from '/@/api/backend/payment'

const loading = ref(false)
const configList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    type: '',
    name: '',
    status: '',
})

const dialogVisible = ref(false)
const dialogTitle = ref('新建支付配置')
const form = reactive({
    config_id: '',
    type: '',
    name: '',
    app_id: '',
    app_secret: '',
    public_key: '',
    private_key: '',
    merchant_id: '',
    gateway_url: '',
    return_url: '',
    notify_url: '',
    currency: 'CNY',
    status: 1,
    config: '',
})

// 获取支付配置列表
const getConfigList = async () => {
    loading.value = true
    try {
        const response = await paymentConfigApi.getList({
            page: page.value,
            limit: limit.value,
            type: searchForm.type,
            name: searchForm.name,
            status: searchForm.status,
        })
        configList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取支付配置列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getConfigList()
}

// 重置
const resetForm = () => {
    searchForm.type = ''
    searchForm.name = ''
    searchForm.status = ''
    page.value = 1
    getConfigList()
}

// 分页
const handleSizeChange = (val: number) => {
    limit.value = val
    getConfigList()
}

const handleCurrentChange = (val: number) => {
    page.value = val
    getConfigList()
}

// 新建支付配置
const handleAdd = () => {
    dialogTitle.value = '新建支付配置'
    form.config_id = ''
    form.type = ''
    form.name = ''
    form.app_id = ''
    form.app_secret = ''
    form.public_key = ''
    form.private_key = ''
    form.merchant_id = ''
    form.gateway_url = ''
    form.return_url = ''
    form.notify_url = ''
    form.currency = 'CNY'
    form.status = 1
    form.config = ''
    dialogVisible.value = true
}

// 编辑支付配置
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑支付配置'
    form.config_id = row.config_id
    form.type = row.type
    form.name = row.name
    form.app_id = row.app_id
    form.app_secret = row.app_secret
    form.public_key = row.public_key
    form.private_key = row.private_key
    form.merchant_id = row.merchant_id
    form.gateway_url = row.gateway_url
    form.return_url = row.return_url
    form.notify_url = row.notify_url
    form.currency = row.currency
    form.status = row.status
    form.config = JSON.stringify(row.config)
    dialogVisible.value = true
}

// 提交表单
const handleSubmit = async () => {
    try {
        if (form.config_id) {
            // 更新支付配置
            await paymentConfigApi.update({
                ...form,
                config: form.config ? JSON.parse(form.config) : {},
            })
            ElMessage.success('更新支付配置成功')
        } else {
            // 创建支付配置
            await paymentConfigApi.create({
                ...form,
                config: form.config ? JSON.parse(form.config) : {},
            })
            ElMessage.success('创建支付配置成功')
        }
        dialogVisible.value = false
        getConfigList()
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 删除支付配置
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该支付配置吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await paymentConfigApi.delete(id)
        ElMessage.success('删除支付配置成功')
        getConfigList()
    } catch (error) {
        // 取消删除
    }
}

// 切换支付配置状态
const handleToggleStatus = async (id: number, status: number) => {
    try {
        await paymentConfigApi.toggleStatus(id)
        ElMessage.success('更新状态成功')
    } catch (error) {
        ElMessage.error('操作失败')
        // 恢复原状态
        getConfigList()
    }
}

onMounted(() => {
    getConfigList()
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
