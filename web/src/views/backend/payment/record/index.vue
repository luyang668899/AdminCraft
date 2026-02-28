<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>支付记录管理</span>
                </div>
            </template>

            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item label="订单ID">
                    <el-input v-model="searchForm.order_id" placeholder="请输入订单ID" />
                </el-form-item>
                <el-form-item label="支付类型">
                    <el-select v-model="searchForm.payment_type" placeholder="请选择支付类型">
                        <el-option label="支付宝" value="alipay" />
                        <el-option label="微信支付" value="wechat" />
                        <el-option label="PayPal" value="paypal" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="待支付" value="0" />
                        <el-option label="已支付" value="1" />
                        <el-option label="支付失败" value="2" />
                        <el-option label="已退款" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">搜索</el-button>
                    <el-button @click="resetForm">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="recordList" style="width: 100%">
                <el-table-column prop="record_id" label="记录ID" width="80" />
                <el-table-column prop="order_id" label="订单ID" />
                <el-table-column prop="payment_type_text" label="支付类型" width="120" />
                <el-table-column prop="transaction_id" label="交易ID" />
                <el-table-column prop="amount" label="支付金额" width="120" />
                <el-table-column prop="currency" label="货币类型" width="100" />
                <el-table-column prop="status_text" label="状态" width="120" />
                <el-table-column prop="pay_time" label="支付时间" width="180" />
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

        <!-- 查看支付记录对话框 -->
        <el-dialog v-model="viewDialogVisible" title="查看支付记录" width="800px">
            <el-form :model="recordDetail" label-width="100px">
                <el-form-item label="记录ID">
                    <el-input v-model="recordDetail.record_id" disabled />
                </el-form-item>
                <el-form-item label="订单ID">
                    <el-input v-model="recordDetail.order_id" disabled />
                </el-form-item>
                <el-form-item label="支付类型">
                    <el-input v-model="recordDetail.payment_type_text" disabled />
                </el-form-item>
                <el-form-item label="交易ID">
                    <el-input v-model="recordDetail.transaction_id" disabled />
                </el-form-item>
                <el-form-item label="支付金额">
                    <el-input v-model="recordDetail.amount" disabled />
                </el-form-item>
                <el-form-item label="货币类型">
                    <el-input v-model="recordDetail.currency" disabled />
                </el-form-item>
                <el-form-item label="状态">
                    <el-input v-model="recordDetail.status_text" disabled />
                </el-form-item>
                <el-form-item label="支付时间">
                    <el-input v-model="recordDetail.pay_time" disabled />
                </el-form-item>
                <el-form-item label="退款时间">
                    <el-input v-model="recordDetail.refund_time" disabled />
                </el-form-item>
                <el-form-item label="通知数据">
                    <el-input v-model="recordDetail.notify_data" type="textarea" :rows="4" disabled />
                </el-form-item>
                <el-form-item label="返回数据">
                    <el-input v-model="recordDetail.return_data" type="textarea" :rows="4" disabled />
                </el-form-item>
                <el-form-item label="支付配置">
                    <el-input v-model="recordDetail.config_name" disabled />
                </el-form-item>
            </el-form>

            <el-divider>支付日志</el-divider>
            <el-table :data="recordLogs" style="width: 100%">
                <el-table-column prop="log_id" label="日志ID" width="80" />
                <el-table-column prop="type" label="日志类型" width="120" />
                <el-table-column prop="content" label="日志内容" />
                <el-table-column prop="created_at" label="创建时间" width="180" />
            </el-table>

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
import { paymentRecordApi } from '/@/api/backend/payment'

const loading = ref(false)
const recordList = ref<any[]>([])
const total = ref(0)
const page = ref(1)
const limit = ref(10)

const searchForm = reactive({
    order_id: '',
    payment_type: '',
    status: '',
})

const viewDialogVisible = ref(false)
const recordDetail = reactive({
    record_id: 0,
    order_id: '',
    payment_type_text: '',
    transaction_id: '',
    amount: '',
    currency: '',
    status_text: '',
    pay_time: '',
    refund_time: '',
    notify_data: '',
    return_data: '',
    config_name: '',
})

const recordLogs = ref<any[]>([])

// 获取支付记录列表
const getRecordList = async () => {
    loading.value = true
    try {
        const response = await paymentRecordApi.getList({
            page: page.value,
            limit: limit.value,
            order_id: searchForm.order_id,
            payment_type: searchForm.payment_type,
            status: searchForm.status,
        })
        recordList.value = response.data.data
        total.value = response.data.count
    } catch (error) {
        ElMessage.error('获取支付记录列表失败')
    } finally {
        loading.value = false
    }
}

// 搜索
const handleSearch = () => {
    page.value = 1
    getRecordList()
}

// 重置
const resetForm = () => {
    searchForm.order_id = ''
    searchForm.payment_type = ''
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

// 查看支付记录
const handleView = async (id: number) => {
    // 这里应该调用API获取详细信息，暂时模拟数据
    recordDetail.record_id = id
    recordDetail.order_id = 'ORDER' + id
    recordDetail.payment_type_text = '支付宝'
    recordDetail.transaction_id = 'TXN' + id
    recordDetail.amount = '100.00'
    recordDetail.currency = 'CNY'
    recordDetail.status_text = '已支付'
    recordDetail.pay_time = new Date().toISOString()
    recordDetail.refund_time = ''
    recordDetail.notify_data = JSON.stringify({ status: 'success' })
    recordDetail.return_data = JSON.stringify({ status: 'success' })
    recordDetail.config_name = '支付宝配置'

    // 获取支付日志
    try {
        const response = await paymentRecordApi.getLogs(id)
        recordLogs.value = response.data.data
    } catch (error) {
        ElMessage.error('获取支付日志失败')
        recordLogs.value = []
    }

    viewDialogVisible.value = true
}

// 删除支付记录
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除该支付记录吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })
        await paymentRecordApi.delete(id)
        ElMessage.success('删除支付记录成功')
        getRecordList()
    } catch (error) {
        // 取消删除
    }
}

onMounted(() => {
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
