<template>
    <div class="ba-content-tag">
        <el-card class="ba-card">
            <template #header>
                <div class="ba-card-header">
                    <span>{{ $t('content.tag.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <el-table v-loading="loading" :data="tagList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="{{ $t('content.tag.name') }}" />
                <el-table-column prop="alias" label="{{ $t('content.tag.alias') }}" width="180" />
                <el-table-column prop="sort" label="{{ $t('content.tag.sort') }}" width="80" />
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
                    <el-form-item label="{{ $t('content.tag.name') }}" prop="name">
                        <el-input v-model="form.name" placeholder="{{ $t('content.tag.enter_name') }}" />
                    </el-form-item>
                    <el-form-item label="{{ $t('content.tag.alias') }}" prop="alias">
                        <el-input v-model="form.alias" placeholder="{{ $t('content.tag.enter_alias') }}" />
                    </el-form-item>
                    <el-form-item label="{{ $t('content.tag.sort') }}" prop="sort">
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
import { tagApi } from '/@/api/backend/content'

// 类型定义
interface Tag {
    id: number
    name: string
    alias: string
    sort: number
    create_time: string
}

// 加载状态
const loading = ref(false)

// 标签列表
const tagList = ref<Tag[]>([])

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
    name: '',
    alias: '',
    sort: 0,
})

// 表单验证规则
const rules = {
    name: [{ required: true, message: '请输入标签名称', trigger: 'blur' }],
}

// 获取标签列表
const getTagList = async () => {
    loading.value = true
    try {
        const res = await tagApi.getList(query)
        if (res.code === 1) {
            tagList.value = res.data.list
            total.value = res.data.total
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('获取标签列表失败')
    } finally {
        loading.value = false
    }
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加标签'
    form.id = 0
    form.name = ''
    form.alias = ''
    form.sort = 0
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (row: Tag) => {
    dialogTitle.value = '编辑标签'
    form.id = row.id
    form.name = row.name
    form.alias = row.alias
    form.sort = row.sort
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除这个标签吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })

        const res = await tagApi.delete({ id })
        if (res.code === 1) {
            ElMessage.success('删除成功')
            getTagList()
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
            res = await tagApi.add(form)
        } else {
            res = await tagApi.edit(form)
        }

        if (res.code === 1) {
            ElMessage.success(form.id === 0 ? '添加成功' : '编辑成功')
            dialogVisible.value = false
            getTagList()
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('提交失败')
        }
    }
}

// 处理分页
const handleSizeChange = (size: number) => {
    query.limit = size
    getTagList()
}

const handleCurrentChange = (current: number) => {
    query.page = current
    getTagList()
}

// 初始化
onMounted(() => {
    getTagList()
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
