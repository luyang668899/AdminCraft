<template>
    <div class="ba-content-category">
        <el-card class="ba-card">
            <template #header>
                <div class="ba-card-header">
                    <span>{{ $t('content.category.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <el-table v-loading="loading" :data="categoryList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="{{ $t('content.category.name') }}">
                    <template #default="scope">
                        <span :style="{ paddingLeft: scope.row.level * 20 + 'px' }">
                            {{ scope.row.name }}
                        </span>
                    </template>
                </el-table-column>
                <el-table-column prop="alias" label="{{ $t('content.category.alias') }}" width="180" />
                <el-table-column prop="sort" label="{{ $t('content.category.sort') }}" width="80" />
                <el-table-column prop="create_time" label="{{ $t('common.create_time') }}" width="180" />
                <el-table-column label="{{ $t('common.action') }}" width="200" fixed="right">
                    <template #default="scope">
                        <el-button size="small" type="primary" @click="handleEdit(scope.row)">
                            {{ $t('common.edit') }}
                        </el-button>
                        <el-button size="small" type="danger" @click="handleDelete(scope.row.id)">
                            {{ $t('common.delete') }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 分页 -->
            <div class="ba-pagination">
                <el-pagination
                    v-model:current-page="query.page"
                    v-model:page-size="query.limit"
                    :page-sizes="[10, 20, 50, 100]"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="total"
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                />
            </div>

            <!-- 添加/编辑对话框 -->
            <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500px">
                <el-form :model="form" :rules="rules" ref="formRef" label-width="100px">
                    <el-form-item label="{{ $t('content.category.parent') }}" prop="pid">
                        <el-select v-model="form.pid" placeholder="{{ $t('content.category.select_parent') }}">
                            <el-option
                                v-for="item in categoryTree"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                                :disabled="item.id === form.id"
                            />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="{{ $t('content.category.name') }}" prop="name">
                        <el-input v-model="form.name" placeholder="{{ $t('content.category.enter_name') }}" />
                    </el-form-item>
                    <el-form-item label="{{ $t('content.category.alias') }}" prop="alias">
                        <el-input v-model="form.alias" placeholder="{{ $t('content.category.enter_alias') }}" />
                    </el-form-item>
                    <el-form-item label="{{ $t('content.category.sort') }}" prop="sort">
                        <el-input-number v-model="form.sort" :min="0" :max="9999" />
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
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { categoryApi } from '/@/api/backend/content'

// 类型定义
interface Category {
    id: number
    pid: number
    name: string
    alias: string
    sort: number
    children?: Category[]
}

// 加载状态
const loading = ref(false)

// 分类列表
const categoryList = ref<Category[]>([])

// 分类树
const categoryTree = ref<Category[]>([])

// 总数
const total = ref(0)

// 查询参数
const query = reactive({
    page: 1,
    limit: 20,
})

// 对话框
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref()

// 表单数据
const form = reactive({
    id: 0,
    pid: 0,
    name: '',
    alias: '',
    sort: 0,
})

// 表单验证规则
const rules = {
    name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
}

// 获取分类列表
const getCategoryList = async () => {
    loading.value = true
    try {
        const res = await categoryApi.getList(query)
        if (res.code === 1) {
            categoryList.value = res.data.list
            total.value = res.data.total
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('获取分类列表失败')
    } finally {
        loading.value = false
    }
}

// 获取分类树
const getCategoryTree = async () => {
    try {
        const res = await categoryApi.getTree({})
        if (res.code === 1) {
            categoryTree.value = res.data
        }
    } catch (error) {
        ElMessage.error('获取分类树失败')
    }
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加分类'
    form.id = 0
    form.pid = 0
    form.name = ''
    form.alias = ''
    form.sort = 0
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (row: Category) => {
    dialogTitle.value = '编辑分类'
    form.id = row.id
    form.pid = row.pid
    form.name = row.name
    form.alias = row.alias
    form.sort = row.sort
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除这个分类吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })

        const res = await categoryApi.delete({ id })
        if (res.code === 1) {
            ElMessage.success('删除成功')
            getCategoryList()
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('删除失败')
        }
    }
}

// 处理提交
const handleSubmit = async () => {
    if (!formRef.value) return

    try {
        await formRef.value.validate()

        let res
        if (form.id === 0) {
            res = await categoryApi.add(form)
        } else {
            res = await categoryApi.edit(form)
        }

        if (res.code === 1) {
            ElMessage.success(form.id === 0 ? '添加成功' : '编辑成功')
            dialogVisible.value = false
            getCategoryList()
            getCategoryTree()
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('提交失败')
        }
    }
}

// 分页
const handleSizeChange = (size: number) => {
    query.limit = size
    getCategoryList()
}

const handleCurrentChange = (current: number) => {
    query.page = current
    getCategoryList()
}

// 初始化
onMounted(() => {
    getCategoryList()
    getCategoryTree()
})
</script>

<style scoped>
.ba-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ba-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
</style>
