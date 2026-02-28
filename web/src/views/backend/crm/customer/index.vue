<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{ $t('crm.customer.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <!-- 搜索条件 -->
            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item :label="$t('crm.customer.keyword')">
                    <el-input v-model="searchForm.keyword" placeholder="{{ $t('crm.customer.enterKeyword') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.status')">
                    <el-select v-model="searchForm.status" placeholder="{{ $t('crm.customer.selectStatus') }}">
                        <el-option label="潜在客户" value="potential" />
                        <el-option label="联系中" value="contact" />
                        <el-option label="已成交" value="deal" />
                        <el-option label="已流失" value="lost" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.customer.level')">
                    <el-select v-model="searchForm.level" placeholder="{{ $t('crm.customer.selectLevel') }}">
                        <el-option label="普通" value="normal" />
                        <el-option label="重要" value="important" />
                        <el-option label="VIP" value="vip" />
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

            <!-- 客户列表 -->
            <el-table :data="customerList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" :label="$t('crm.customer.name')" width="120" />
                <el-table-column prop="phone" :label="$t('crm.customer.phone')" width="150" />
                <el-table-column prop="email" :label="$t('crm.customer.email')" width="200" />
                <el-table-column prop="company" :label="$t('crm.customer.company')" width="180" />
                <el-table-column prop="statusText" :label="$t('crm.customer.status')" width="100" />
                <el-table-column prop="levelText" :label="$t('crm.customer.level')" width="100" />
                <el-table-column prop="source" :label="$t('crm.customer.source')" width="120" />
                <el-table-column prop="create_time" :label="$t('common.createTime')" width="180" />
                <el-table-column :label="$t('common.action')" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleEdit(row)">
                            {{ $t('common.edit') }}
                        </el-button>
                        <el-button size="small" type="primary" @click="handleFollowList(row.id)">
                            {{ $t('crm.customer.followList') }}
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
                <el-form-item :label="$t('crm.customer.name')" required>
                    <el-input v-model="form.name" placeholder="{{ $t('crm.customer.enterName') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.phone')" required>
                    <el-input v-model="form.phone" placeholder="{{ $t('crm.customer.enterPhone') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.email')">
                    <el-input v-model="form.email" placeholder="{{ $t('crm.customer.enterEmail') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.company')">
                    <el-input v-model="form.company" placeholder="{{ $t('crm.customer.enterCompany') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.industry')">
                    <el-input v-model="form.industry" placeholder="{{ $t('crm.customer.enterIndustry') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.address')">
                    <el-input v-model="form.address" placeholder="{{ $t('crm.customer.enterAddress') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.status')">
                    <el-select v-model="form.status" placeholder="{{ $t('crm.customer.selectStatus') }}">
                        <el-option label="潜在客户" value="potential" />
                        <el-option label="联系中" value="contact" />
                        <el-option label="已成交" value="deal" />
                        <el-option label="已流失" value="lost" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.customer.level')">
                    <el-select v-model="form.level" placeholder="{{ $t('crm.customer.selectLevel') }}">
                        <el-option label="普通" value="normal" />
                        <el-option label="重要" value="important" />
                        <el-option label="VIP" value="vip" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('crm.customer.source')">
                    <el-input v-model="form.source" placeholder="{{ $t('crm.customer.enterSource') }}" />
                </el-form-item>
                <el-form-item :label="$t('crm.customer.remark')">
                    <el-input v-model="form.remark" type="textarea" :rows="4" placeholder="{{ $t('crm.customer.enterRemark') }}" />
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

        <!-- 跟进记录对话框 -->
        <el-dialog v-model="followDialogVisible" title="跟进记录" width="800px">
            <div class="flex justify-end mb-4">
                <el-button type="primary" @click="handleAddFollow"> 添加跟进记录 </el-button>
            </div>
            <el-table :data="followList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="followTypeText" label="跟进方式" width="120" />
                <el-table-column prop="content" label="跟进内容" min-width="300" />
                <el-table-column prop="next_time" label="下次跟进时间" width="180" />
                <el-table-column prop="admin.username" label="跟进人" width="120" />
                <el-table-column prop="create_time" label="创建时间" width="180" />
            </el-table>
            <el-pagination
                v-model:current-page="followPageInfo.page"
                v-model:page-size="followPageInfo.limit"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper"
                :total="followPageInfo.total"
                @size-change="handleFollowSizeChange"
                @current-change="handleFollowCurrentChange"
                class="mt-4"
            />
        </el-dialog>

        <!-- 添加跟进记录对话框 -->
        <el-dialog v-model="addFollowDialogVisible" title="添加跟进记录" width="600px">
            <el-form :model="followForm" label-width="120px">
                <el-form-item label="跟进方式" required>
                    <el-select v-model="followForm.follow_type" placeholder="请选择跟进方式">
                        <el-option label="电话" value="call" />
                        <el-option label="邮件" value="email" />
                        <el-option label="拜访" value="visit" />
                        <el-option label="其他" value="other" />
                    </el-select>
                </el-form-item>
                <el-form-item label="跟进内容" required>
                    <el-input v-model="followForm.content" type="textarea" :rows="4" placeholder="请输入跟进内容" />
                </el-form-item>
                <el-form-item label="下次跟进时间">
                    <el-date-picker v-model="followForm.next_time" type="datetime" placeholder="请选择下次跟进时间" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="addFollowDialogVisible = false">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" @click="handleSubmitFollow">
                        {{ $t('common.submit') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { customerApi } from '/@/api/backend/crm'
import { ElMessage } from 'element-plus'

const customerList = ref<any[]>([])
const followList = ref<any[]>([])
const dialogVisible = ref(false)
const followDialogVisible = ref(false)
const addFollowDialogVisible = ref(false)
const dialogTitle = ref('')
const currentCustomerId = ref(0)

const searchForm = reactive({
    keyword: '',
    status: '',
    level: '',
})

const pageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const followPageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const form = reactive({
    id: 0,
    name: '',
    phone: '',
    email: '',
    company: '',
    industry: '',
    address: '',
    status: 'potential',
    level: 'normal',
    source: '',
    remark: '',
})

const followForm = reactive({
    customer_id: 0,
    follow_type: 'call',
    content: '',
    next_time: '',
})

// 加载客户列表
const loadCustomerList = async () => {
    try {
        const response = await customerApi.getList({
            ...searchForm,
            page: pageInfo.page,
            limit: pageInfo.limit,
        })
        customerList.value = response.data.rows
        pageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取客户列表失败')
    }
}

// 加载跟进记录列表
const loadFollowList = async () => {
    try {
        const response = await customerApi.getFollowList(currentCustomerId.value, {
            page: followPageInfo.page,
            limit: followPageInfo.limit,
        })
        followList.value = response.data.rows
        followPageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取跟进记录失败')
    }
}

// 处理搜索
const handleSearch = () => {
    pageInfo.page = 1
    loadCustomerList()
}

// 重置搜索
const resetSearch = () => {
    Object.assign(searchForm, {
        keyword: '',
        status: '',
        level: '',
    })
    pageInfo.page = 1
    loadCustomerList()
}

// 处理分页大小变化
const handleSizeChange = (size: number) => {
    pageInfo.limit = size
    loadCustomerList()
}

// 处理页码变化
const handleCurrentChange = (current: number) => {
    pageInfo.page = current
    loadCustomerList()
}

// 处理跟进记录分页大小变化
const handleFollowSizeChange = (size: number) => {
    followPageInfo.limit = size
    loadFollowList()
}

// 处理跟进记录页码变化
const handleFollowCurrentChange = (current: number) => {
    followPageInfo.page = current
    loadFollowList()
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加客户'
    Object.assign(form, {
        id: 0,
        name: '',
        phone: '',
        email: '',
        company: '',
        industry: '',
        address: '',
        status: 'potential',
        level: 'normal',
        source: '',
        remark: '',
    })
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑客户'
    Object.assign(form, row)
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        const response = await customerApi.delete(id)
        ElMessage.success(response.msg)
        loadCustomerList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '删除失败')
    }
}

// 处理查看跟进记录
const handleFollowList = (customer_id: number) => {
    currentCustomerId.value = customer_id
    followPageInfo.page = 1
    loadFollowList()
    followDialogVisible.value = true
}

// 处理添加跟进记录
const handleAddFollow = () => {
    followForm.customer_id = currentCustomerId.value
    followForm.follow_type = 'call'
    followForm.content = ''
    followForm.next_time = ''
    addFollowDialogVisible.value = true
}

// 处理提交
const handleSubmit = async () => {
    try {
        let response
        if (form.id === 0) {
            response = await customerApi.add(form)
        } else {
            response = await customerApi.edit(form.id, form)
        }
        ElMessage.success(response.msg)
        dialogVisible.value = false
        loadCustomerList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

// 处理提交跟进记录
const handleSubmitFollow = async () => {
    try {
        const response = await customerApi.addFollow(followForm)
        ElMessage.success(response.msg)
        addFollowDialogVisible.value = false
        loadFollowList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

onMounted(() => {
    loadCustomerList()
})
</script>

<style scoped>
.app-container {
    padding: 20px;
}
</style>
