<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{ $t('ecommerce.order.title') }}</span>
                </div>
            </template>

            <!-- 搜索条件 -->
            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item :label="$t('ecommerce.order.keyword')">
                    <el-input v-model="searchForm.keyword" placeholder="{{ $t('ecommerce.order.enterKeyword') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.order.orderStatus')">
                    <el-select v-model="searchForm.order_status" placeholder="{{ $t('ecommerce.order.selectOrderStatus') }}">
                        <el-option label="待处理" value="pending" />
                        <el-option label="处理中" value="processing" />
                        <el-option label="已完成" value="completed" />
                        <el-option label="已取消" value="cancelled" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('ecommerce.order.paymentStatus')">
                    <el-select v-model="searchForm.payment_status" placeholder="{{ $t('ecommerce.order.selectPaymentStatus') }}">
                        <el-option label="未支付" value="0" />
                        <el-option label="已支付" value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('ecommerce.order.startTime')">
                    <el-date-picker v-model="searchForm.start_time" type="date" placeholder="开始时间" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.order.endTime')">
                    <el-date-picker v-model="searchForm.end_time" type="date" placeholder="结束时间" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">
                        {{ $t('common.search') }}
                    </el-button>
                    <el-button @click="resetSearch">
                        {{ $t('common.reset') }}
                    </el-button>
                </el-form-item>
            </el-form>

            <!-- 订单列表 -->
            <el-table :data="orderList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="order_sn" :label="$t('ecommerce.order.orderSn')" width="180" />
                <el-table-column prop="user_id" :label="$t('ecommerce.order.userId')" width="100" />
                <el-table-column prop="total_amount" :label="$t('ecommerce.order.totalAmount')" width="120" />
                <el-table-column prop="payment_amount" :label="$t('ecommerce.order.paymentAmount')" width="120" />
                <el-table-column prop="payment_method" :label="$t('ecommerce.order.paymentMethod')" width="120" />
                <el-table-column prop="paymentStatusText" :label="$t('ecommerce.order.paymentStatus')" width="100" />
                <el-table-column prop="orderStatusText" :label="$t('ecommerce.order.orderStatus')" width="100" />
                <el-table-column prop="create_time" :label="$t('common.createTime')" width="180" />
                <el-table-column :label="$t('common.action')" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleView(row.id)">
                            {{ $t('common.view') }}
                        </el-button>
                        <el-button size="small" type="primary" @click="handleUpdateStatus(row)">
                            {{ $t('ecommerce.order.updateStatus') }}
                        </el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">
                            {{ $t('common.delete') }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 分页 -->
            <el-pagination
                v-model:current-page="pageInfo.page"
                v-model:page-size="pageInfo.limit"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper"
                :total="pageInfo.total"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                class="mt-4"
            />
        </el-card>

        <!-- 订单详情对话框 -->
        <el-dialog v-model="viewDialogVisible" title="订单详情" width="800px">
            <div v-if="orderDetail" class="order-detail">
                <el-descriptions :column="1" border>
                    <el-descriptions-item :label="$t('ecommerce.order.orderSn')">{{ orderDetail.order_sn }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.userId')">{{ orderDetail.user_id }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.totalAmount')">{{ orderDetail.total_amount }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.paymentAmount')">{{ orderDetail.payment_amount }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.paymentMethod')">{{ orderDetail.payment_method }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.paymentStatus')">{{ orderDetail.paymentStatusText }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.orderStatus')">{{ orderDetail.orderStatusText }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.shippingName')">{{ orderDetail.shipping_name }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.shippingPhone')">{{ orderDetail.shipping_phone }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('ecommerce.order.shippingAddress')">{{ orderDetail.shipping_address }}</el-descriptions-item>
                    <el-descriptions-item :label="$t('common.createTime')">{{ orderDetail.create_time }}</el-descriptions-item>
                </el-descriptions>

                <h3 class="mt-4 mb-2">订单商品</h3>
                <el-table :data="orderDetail.orderGoods" style="width: 100%">
                    <el-table-column prop="goods_name" label="商品名称" min-width="200" />
                    <el-table-column prop="goods_sn" label="商品编号" width="150" />
                    <el-table-column prop="price" label="价格" width="100" />
                    <el-table-column prop="quantity" label="数量" width="100" />
                    <el-table-column prop="spec_info" label="规格信息" width="150" />
                </el-table>

                <h3 class="mt-4 mb-2">支付记录</h3>
                <el-table :data="orderDetail.paymentLogs" style="width: 100%">
                    <el-table-column prop="payment_method" label="支付方式" width="150" />
                    <el-table-column prop="amount" label="支付金额" width="100" />
                    <el-table-column prop="transaction_id" label="交易ID" width="200" />
                    <el-table-column prop="statusText" label="支付状态" width="100" />
                    <el-table-column prop="create_time" label="创建时间" width="180" />
                </el-table>
            </div>
        </el-dialog>

        <!-- 更新订单状态对话框 -->
        <el-dialog v-model="statusDialogVisible" title="更新订单状态" width="400px">
            <el-form :model="statusForm" label-width="120px">
                <el-form-item :label="$t('ecommerce.order.orderStatus')" required>
                    <el-select v-model="statusForm.order_status" placeholder="{{ $t('ecommerce.order.selectOrderStatus') }}">
                        <el-option label="待处理" value="pending" />
                        <el-option label="处理中" value="processing" />
                        <el-option label="已完成" value="completed" />
                        <el-option label="已取消" value="cancelled" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="statusDialogVisible = false">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" @click="handleStatusSubmit">
                        {{ $t('common.submit') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { orderApi } from '/@/api/backend/ecommerce'
import { ElMessage } from 'element-plus'

const orderList = ref<any[]>([])
const viewDialogVisible = ref(false)
const statusDialogVisible = ref(false)
const orderDetail = ref<any>(null)
const currentOrderId = ref(0)

const searchForm = reactive({
    keyword: '',
    order_status: '',
    payment_status: '',
    start_time: '',
    end_time: '',
})

const pageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const statusForm = reactive({
    order_status: '',
})

// 加载订单列表
const loadOrderList = async () => {
    try {
        const response = await orderApi.getList({
            ...searchForm,
            page: pageInfo.page,
            limit: pageInfo.limit,
        })
        orderList.value = response.data.rows
        pageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取订单列表失败')
    }
}

// 处理搜索
const handleSearch = () => {
    pageInfo.page = 1
    loadOrderList()
}

// 重置搜索
const resetSearch = () => {
    Object.assign(searchForm, {
        keyword: '',
        order_status: '',
        payment_status: '',
        start_time: '',
        end_time: '',
    })
    pageInfo.page = 1
    loadOrderList()
}

// 处理分页大小变化
const handleSizeChange = (size: number) => {
    pageInfo.limit = size
    loadOrderList()
}

// 处理页码变化
const handleCurrentChange = (current: number) => {
    pageInfo.page = current
    loadOrderList()
}

// 处理查看订单详情
const handleView = async (id: number) => {
    try {
        const response = await orderApi.view(id)
        orderDetail.value = response.data
        viewDialogVisible.value = true
    } catch (error) {
        ElMessage.error('获取订单详情失败')
    }
}

// 处理更新订单状态
const handleUpdateStatus = (row: any) => {
    currentOrderId.value = row.id
    statusForm.order_status = row.order_status
    statusDialogVisible.value = true
}

// 处理状态提交
const handleStatusSubmit = async () => {
    try {
        const response = await orderApi.updateStatus(currentOrderId.value, statusForm)
        ElMessage.success(response.msg)
        statusDialogVisible.value = false
        loadOrderList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        const response = await orderApi.delete(id)
        ElMessage.success(response.msg)
        loadOrderList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '删除失败')
    }
}

onMounted(() => {
    loadOrderList()
})
</script>

<style scoped>
.app-container {
    padding: 20px;
}

.order-detail {
    line-height: 1.5;
}
</style>
