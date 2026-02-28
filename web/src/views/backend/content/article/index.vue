<template>
    <div class="ba-content-article">
        <el-card class="ba-card">
            <template #header>
                <div class="ba-card-header">
                    <span>{{ $t('content.article.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <!-- 搜索 -->
            <div class="ba-search">
                <el-form :inline="true" :model="searchForm" class="demo-form-inline">
                    <el-form-item label="分类">
                        <el-select v-model="searchForm.category_id" placeholder="选择分类" clearable>
                            <el-option v-for="item in categoryTree" :key="item.id" :label="item.name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="状态">
                        <el-select v-model="searchForm.status" placeholder="选择状态" clearable>
                            <el-option label="发布" value="1" />
                            <el-option label="草稿" value="0" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="关键词">
                        <el-input v-model="searchForm.keyword" placeholder="输入标题或内容" style="width: 200px" />
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
            </div>

            <el-table v-loading="loading" :data="articleList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="title" label="{{ $t('content.article.title') }}" />
                <el-table-column prop="category_name" label="分类" width="180" />
                <el-table-column prop="tags" label="标签" width="200">
                    <template #default="scope">
                        <el-tag v-for="tag in scope.row.tags" :key="tag.id" size="small" style="margin-right: 5px">
                            {{ tag.name }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="scope">
                        <el-tag :type="scope.row.status === 1 ? 'success' : 'info'">
                            {{ scope.row.status === 1 ? '发布' : '草稿' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="{{ $t('common.create_time') }}" width="180" />
                <el-table-column label="{{ $t('common.action') }}" width="300" fixed="right">
                    <template #default="scope">
                        <el-button size="small" type="primary" @click="handleEdit(scope.row)">
                            {{ $t('common.edit') }}
                        </el-button>
                        <el-button v-if="scope.row.status === 0" size="small" type="success" @click="handlePublish(scope.row.id)"> 发布 </el-button>
                        <el-button v-else size="small" type="warning" @click="handleDraft(scope.row.id)"> 草稿 </el-button>
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
            <el-dialog v-model="dialogVisible" :title="dialogTitle" width="800px" destroy-on-close>
                <el-form :model="form" :rules="rules" ref="formRef" label-width="100px">
                    <el-form-item label="{{ $t('content.article.title') }}" prop="title">
                        <el-input v-model="form.title" placeholder="{{ $t('content.article.enter_title') }}" />
                    </el-form-item>
                    <el-form-item label="分类" prop="category_id">
                        <el-select v-model="form.category_id" placeholder="选择分类">
                            <el-option v-for="item in categoryTree" :key="item.id" :label="item.name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="标签" prop="tags">
                        <el-select v-model="form.tags" multiple placeholder="选择标签">
                            <el-option v-for="item in tagList" :key="item.id" :label="item.name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="{{ $t('content.article.content') }}" prop="content">
                        <ba-input v-model="form.content" type="editor" placeholder="{{ $t('content.article.enter_content') }}" :height="400" />
                    </el-form-item>
                    <el-form-item label="{{ $t('content.article.status') }}" prop="status">
                        <el-radio-group v-model="form.status">
                            <el-radio :label="1">发布</el-radio>
                            <el-radio :label="0">草稿</el-radio>
                        </el-radio-group>
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
import { articleApi, categoryApi, tagApi } from '/@/api/backend/content'
import baInput from '/@/components/baInput/index.vue'

// 类型定义
interface Category {
    id: number
    name: string
    children?: Category[]
}

interface Tag {
    id: number
    name: string
}

interface Article {
    id: number
    title: string
    category_id: number
    tags: number[]
    content: string
    status: number
    created_at: string
    updated_at: string
    category?: {
        name: string
    }
    tag_list?: {
        name: string
    }[]
}

// 加载状态
const loading = ref(false)

// 文章列表
const articleList = ref<Article[]>([])

// 分类树
const categoryTree = ref<Category[]>([])

// 标签列表
const tagList = ref<Tag[]>([])

// 总数
const total = ref(0)

// 查询参数
const query = reactive({
    page: 1,
    limit: 20,
})

// 搜索表单
const searchForm = reactive({
    category_id: '',
    status: '',
    keyword: '',
})

// 对话框
const dialogVisible = ref(false)
const dialogTitle = ref('')
const formRef = ref()

// 表单数据
const form = reactive({
    id: 0,
    title: '',
    category_id: '',
    tags: [],
    content: '',
    status: 0,
})

// 表单验证规则
const rules = {
    title: [{ required: true, message: '请输入文章标题', trigger: 'blur' }],
    category_id: [{ required: true, message: '请选择分类', trigger: 'blur' }],
    content: [{ required: true, message: '请输入文章内容', trigger: 'blur' }],
}

// 获取文章列表
const getArticleList = async () => {
    loading.value = true
    try {
        const params = {
            ...query,
            ...searchForm,
        }
        const res = await articleApi.getList(params)
        if (res.code === 1) {
            articleList.value = res.data.list
            total.value = res.data.total
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('获取文章列表失败')
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

// 获取标签列表
const getTagList = async () => {
    try {
        const res = await tagApi.getList({ limit: 100 })
        if (res.code === 1) {
            tagList.value = res.data.list
        }
    } catch (error) {
        ElMessage.error('获取标签列表失败')
    }
}

// 处理搜索
const handleSearch = () => {
    query.page = 1
    getArticleList()
}

// 重置搜索
const resetSearch = () => {
    searchForm.category_id = ''
    searchForm.status = ''
    searchForm.keyword = ''
    query.page = 1
    getArticleList()
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加文章'
    form.id = 0
    form.title = ''
    form.category_id = ''
    form.tags = []
    form.content = ''
    form.status = 0
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = async (row: Article) => {
    try {
        const res = await articleApi.getInfo({ id: row.id })
        if (res.code === 1) {
            dialogTitle.value = '编辑文章'
            form.id = res.data.id
            form.title = res.data.title
            form.category_id = res.data.category_id
            form.tags = res.data.tags.map((tag: { id: number }) => tag.id)
            form.content = res.data.content
            form.status = res.data.status
            dialogVisible.value = true
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('获取文章详情失败')
    }
}

// 处理发布
const handlePublish = async (id: number) => {
    try {
        const res = await articleApi.publish({ id })
        if (res.code === 1) {
            ElMessage.success('发布成功')
            getArticleList()
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('发布失败')
    }
}

// 处理草稿
const handleDraft = async (id: number) => {
    try {
        const res = await articleApi.draft({ id })
        if (res.code === 1) {
            ElMessage.success('已转为草稿')
            getArticleList()
        } else {
            ElMessage.error(res.msg)
        }
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        await ElMessageBox.confirm('确定要删除这篇文章吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        })

        const res = await articleApi.delete({ id })
        if (res.code === 1) {
            ElMessage.success('删除成功')
            getArticleList()
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
            res = await articleApi.add(form)
        } else {
            res = await articleApi.edit(form)
        }

        if (res.code === 1) {
            ElMessage.success(form.id === 0 ? '添加成功' : '编辑成功')
            dialogVisible.value = false
            getArticleList()
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
    getArticleList()
}

const handleCurrentChange = (current: number) => {
    query.page = current
    getArticleList()
}

// 初始化
onMounted(() => {
    getArticleList()
    getCategoryTree()
    getTagList()
})
</script>

<style scoped>
.ba-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ba-search {
    margin-bottom: 20px;
}

.ba-pagination {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
</style>
