<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{ $t('crm.salesFunnel.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <!-- 搜索条件 -->
            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item :label="$t('crm.salesFunnel.keyword')">
                    <el-input v-model="searchForm.keyword" placeholder="{{ $t('crm.salesFunnel.enterKeyword') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.stage')">
                    <el-select v-model="searchForm.stage" placeholder="{{ $t('crm.salesFunnel.selectStage') }}">
                        <el-option label="线索" value="lead" />
                        <el-option label="机会" value="opportunity" />
                        <el-option label="提案" value="proposal" />
                        <el-option label="谈判" value="negotiation" />
                        <el-option label="成交" value="closed" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.status')">
                    <el-select v-model="searchForm.status" placeholder="{{ $t('crm.salesFunnel.selectStatus') }}">
                        <el-option label="进行中" value="active" />
                        <el-option label="已赢单" value="won" />
                        <el-option label="已输单" value="lost" />
                    </el-select>
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

            <!-- 销售漏斗列表 -->
            <el-table :data="funnelList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="customer.name" :label="$t('crm.salesFunnel.customer')" width="150" />
                <el-table-column prop="stageText" :label="$t('crm.salesFunnel.stage')" width="100" />
                <el-table-column prop="amount" :label="$t('crm.salesFunnel.amount')" width="120" />
                <el-table-column prop="probability" :label="$t('crm.salesFunnel.probability')" width="100">
                    <template #default="{ row }"> {{ row.probability }}% </template>
                </el-table-column>
                <el-table-column prop="expected_close_date" :label="$t('crm.salesFunnel.expectedCloseDate')" width="180" />
                <el-table-column prop="statusText" :label="$t('crm.salesFunnel.status')" width="100" />
                <el-table-column prop="create_time" :label="$t('common.createTime')" width="180" />
                <el-table-column :label="$t('common.action')" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleEdit(row)">
                            {{ $t('common.edit') }}
                        </el-button>
                        <el-button size="small" type="primary" @click="handleHistory(row.id)">
                            {{ $t('crm.salesFunnel.history') }}
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

        <!-- 添加/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="800px">
            <el-form :model="form" label-width="120px">
                <el-form-item :label="$t('crm.salesFunnel.customer')" required>
                    <el-select v-model="form.customer_id" placeholder="{{ $t('crm.salesFunnel.selectCustomer') }}">
                        <el-option v-for="customer in customers" :key="customer.id" :label="customer.name" :value="customer.id" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.stage')" required>
                    <el-select v-model="form.stage" placeholder="{{ $t('crm.salesFunnel.selectStage') }}">
                        <el-option label="线索" value="lead" />
                        <el-option label="机会" value="opportunity" />
                        <el-option label="提案" value="proposal" />
                        <el-option label="谈判" value="negotiation" />
                        <el-option label="成交" value="closed" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.amount')" required>
                    <el-input v-model.number="form.amount" type="number" placeholder="{{ $t('crm.salesFunnel.enterAmount') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.probability')" required>
                    <el-input
                        v-model.number="form.probability"
                        type="number"
                        :min="0"
                        :max="100"
                        placeholder="{{ $t('crm.salesFunnel.enterProbability') }}"
                    />
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.expectedCloseDate')">
                    <el-date-picker
                        v-model="form.expected_close_date"
                        type="date"
                        placeholder="{{ $t('crm.salesFunnel.selectExpectedCloseDate') }}"
                    />
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.status')">
                    <el-select v-model="form.status" placeholder="{{ $t('crm.salesFunnel.selectStatus') }}">
                        <el-option label="进行中" value="active" />
                        <el-option label="已赢单" value="won" />
                        <el-option label="已输单" value="lost" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.salesFunnel.remark')">
                    <el-input v-model="form.remark" type="textarea" :rows="4" placeholder="{{ $t('crm.salesFunnel.enterRemark') }}" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" @click="handleSubmit">
                        {{ $t('common.submit') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 阶段历史对话框 -->
        <el-dialog v-model="historyDialogVisible" title="阶段历史" width="800px">
            <el-table :data="historyList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="oldStageText" label="旧阶段" width="120" />
                <el-table-column prop="newStageText" label="新阶段" width="120" />
                <el-table-column prop="admin.username" label="操作人" width="120" />
                <el-table-column prop="create_time" label="操作时间" width="180" />
            </el-table>
            <el-pagination
                v-model:current-page="historyPageInfo.page"
                v-model:page-size="historyPageInfo.limit"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper"
                :total="historyPageInfo.total"
                @size-change="handleHistorySizeChange"
                @current-change="handleHistoryCurrentChange"
                class="mt-4"
            />
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { salesFunnelApi } from '/@/api/backend/crm'
import { ElMessage } from 'element-plus'

const funnelList = ref<any[]>([])
const historyList = ref<any[]>([])
const customers = ref<any[]>([])
const dialogVisible = ref(false)
const historyDialogVisible = ref(false)
const dialogTitle = ref('')
const currentFunnelId = ref(0)

const searchForm = reactive({
    keyword: '',
    stage: '',
    status: '',
})

const pageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const historyPageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const form = reactive({
    id: 0,
    customer_id: '',
    stage: 'lead',
    amount: 0,
    probability: 0,
    expected_close_date: '',
    status: 'active',
    remark: '',
})

// 加载销售漏斗列表
const loadFunnelList = async () => {
    try {
        const response = await salesFunnelApi.getList({
            ...searchForm,
            page: pageInfo.page,
            limit: pageInfo.limit,
        })
        funnelList.value = response.data.rows
        pageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取销售漏斗列表失败')
    }
}

// 加载阶段历史列表
const loadHistoryList = async () => {
    try {
        const response = await salesFunnelApi.getHistory(currentFunnelId.value, {
            page: historyPageInfo.page,
            limit: historyPageInfo.limit,
        })
        historyList.value = response.data.rows
        historyPageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取阶段历史失败')
    }
}

// 加载客户列表
const loadCustomers = async () => {
    try {
        // 这里需要调用获取客户列表的API，暂时使用模拟数据
        // 实际项目中应该调用customerApi.getList()
        customers.value = []
    } catch (error) {
        ElMessage.error('获取客户列表失败')
    }
}

// 处理搜索
const handleSearch = () => {
    pageInfo.page = 1
    loadFunnelList()
}

// 重置搜索
const resetSearch = () => {
    Object.assign(searchForm, {
        keyword: '',
        stage: '',
        status: '',
    })
    pageInfo.page = 1
    loadFunnelList()
}

// 处理分页大小变化
const handleSizeChange = (size: number) => {
    pageInfo.limit = size
    loadFunnelList()
}

// 处理页码变化
const handleCurrentChange = (current: number) => {
    pageInfo.page = current
    loadFunnelList()
}

// 处理历史记录分页大小变化
const handleHistorySizeChange = (size: number) => {
    historyPageInfo.limit = size
    loadHistoryList()
}

// 处理历史记录页码变化
const handleHistoryCurrentChange = (current: number) => {
    historyPageInfo.page = current
    loadHistoryList()
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加销售漏斗'
    Object.assign(form, {
        id: 0,
        customer_id: '',
        stage: 'lead',
        amount: 0,
        probability: 0,
        expected_close_date: '',
        status: 'active',
        remark: '',
    })
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑销售漏斗'
    Object.assign(form, row)
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        const response = await salesFunnelApi.delete(id)
        ElMessage.success(response.msg)
        loadFunnelList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '删除失败')
    }
}

// 处理查看阶段历史
const handleHistory = (funnel_id: number) => {
    currentFunnelId.value = funnel_id
    historyPageInfo.page = 1
    loadHistoryList()
    historyDialogVisible.value = true
}

// 处理提交
const handleSubmit = async () => {
    try {
        let response
        if (form.id === 0) {
            response = await salesFunnelApi.add(form)
        } else {
            response = await salesFunnelApi.edit(form.id, form)
        }
        ElMessage.success(response.msg)
        dialogVisible.value = false
        loadFunnelList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

onMounted(() => {
    loadFunnelList()
    loadCustomers()
})
</script>

<style scoped>
.app-container {
    padding: 20px;
}
</style>
