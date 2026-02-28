<template>
    <div class="app-container">
        <el-card>
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{ $t('ecommerce.goods.title') }}</span>
                    <el-button type="primary" @click="handleAdd">
                        {{ $t('common.add') }}
                    </el-button>
                </div>
            </template>

            <!-- 搜索条件 -->
            <el-form :inline="true" :model="searchForm" class="mb-4">
                <el-form-item :label="$t('ecommerce.goods.keyword')">
                    <el-input v-model="searchForm.keyword" placeholder="{{ $t('ecommerce.goods.enterKeyword') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.category')">
                    <el-select v-model="searchForm.category_id" placeholder="{{ $t('ecommerce.goods.selectCategory') }}">
                        <el-option v-for="(value, key) in categoryList" :key="key" :label="value" :value="Number(key)" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.status')">
                    <el-select v-model="searchForm.status" placeholder="{{ $t('ecommerce.goods.selectStatus') }}">
                        <el-option label="上架" value="1" />
                        <el-option label="下架" value="0" />
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

            <!-- 商品列表 -->
            <el-table :data="goodsList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" :label="$t('ecommerce.goods.name')" min-width="200" />
                <el-table-column prop="sn" :label="$t('ecommerce.goods.sn')" width="150" />
                <el-table-column prop="category.name" :label="$t('ecommerce.goods.category')" width="180" />
                <el-table-column prop="price" :label="$t('ecommerce.goods.price')" width="100" />
                <el-table-column prop="stock" :label="$t('ecommerce.goods.stock')" width="100" />
                <el-table-column prop="sales" :label="$t('ecommerce.goods.sales')" width="100" />
                <el-table-column prop="statusText" :label="$t('ecommerce.goods.status')" width="100" />
                <el-table-column prop="create_time" :label="$t('common.createTime')" width="180" />
                <el-table-column :label="$t('common.action')" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleEdit(row)">
                            {{ $t('common.edit') }}
                        </el-button>
                        <el-button size="small" :type="row.status === 1 ? 'warning' : 'success'" @click="handleToggleStatus(row.id)">
                            {{ row.status === 1 ? $t('ecommerce.goods.offline') : $t('ecommerce.goods.online') }}
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
                <el-form-item :label="$t('ecommerce.goods.category')" required>
                    <el-select v-model="form.category_id" placeholder="{{ $t('ecommerce.goods.selectCategory') }}">
                        <el-option v-for="(value, key) in categoryList" :key="key" :label="value" :value="Number(key)" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.name')" required>
                    <el-input v-model="form.name" placeholder="{{ $t('ecommerce.goods.enterName') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.sn')" required>
                    <el-input v-model="form.sn" placeholder="{{ $t('ecommerce.goods.enterSn') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.price')" required>
                    <el-input v-model.number="form.price" type="number" placeholder="{{ $t('ecommerce.goods.enterPrice') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.stock')" required>
                    <el-input v-model.number="form.stock" type="number" placeholder="{{ $t('ecommerce.goods.enterStock') }}" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.status')">
                    <el-switch v-model="form.status" />
                </el-form-item>
                <el-form-item :label="$t('ecommerce.goods.description')">
                    <el-input v-model="form.description" type="textarea" :rows="4" placeholder="{{ $t('ecommerce.goods.enterDescription') }}" />
                </el-form-item>

                <!-- 商品属性 -->
                <el-form-item :label="$t('ecommerce.goods.attributes')">
                    <div v-for="(attr, index) in form.attributes" :key="index" class="flex gap-2 mb-2">
                        <el-input v-model="attr.name" placeholder="属性名称" style="width: 150px" />
                        <el-input v-model="attr.value" placeholder="属性值" style="flex: 1" />
                        <el-button type="danger" size="small" @click="form.attributes.splice(index, 1)">
                            {{ $t('common.delete') }}
                        </el-button>
                    </div>
                    <el-button type="primary" size="small" @click="form.attributes.push({ name: '', value: '' })">
                        {{ $t('ecommerce.goods.addAttribute') }}
                    </el-button>
                </el-form-item>

                <!-- 商品规格 -->
                <el-form-item :label="$t('ecommerce.goods.specs')">
                    <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-2 mb-2">
                        <el-input v-model="spec.spec_name" placeholder="规格名称" style="width: 120px" />
                        <el-input v-model="spec.spec_value" placeholder="规格值" style="width: 120px" />
                        <el-input v-model.number="spec.price" type="number" placeholder="价格" style="width: 100px" />
                        <el-input v-model.number="spec.stock" type="number" placeholder="库存" style="width: 100px" />
                        <el-button type="danger" size="small" @click="form.specs.splice(index, 1)">
                            {{ $t('common.delete') }}
                        </el-button>
                    </div>
                    <el-button type="primary" size="small" @click="form.specs.push({ spec_name: '', spec_value: '', price: 0, stock: 0 })">
                        {{ $t('ecommerce.goods.addSpec') }}
                    </el-button>
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
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { goodsApi } from '/@/api/backend/ecommerce'
import { ElMessage } from 'element-plus'

const goodsList = ref<any[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('')
const categoryList = ref<Record<string, string>>({})

const searchForm = reactive({
    keyword: '',
    category_id: '',
    status: '',
})

const pageInfo = reactive({
    page: 1,
    limit: 10,
    total: 0,
})

const form = reactive({
    id: 0,
    category_id: '',
    name: '',
    sn: '',
    price: 0,
    stock: 0,
    status: 1,
    description: '',
    attributes: [] as { name: string; value: string }[],
    specs: [] as { spec_name: string; spec_value: string; price: number; stock: number }[],
})

// 加载商品列表
const loadGoodsList = async () => {
    try {
        const response = await goodsApi.getList({
            ...searchForm,
            page: pageInfo.page,
            limit: pageInfo.limit,
        })
        goodsList.value = response.data.rows
        pageInfo.total = response.data.total
    } catch (error) {
        ElMessage.error('获取商品列表失败')
    }
}

// 加载分类下拉列表
const loadCategoryList = async () => {
    try {
        // 这里需要调用获取分类列表的API，暂时使用模拟数据
        // 实际项目中应该调用categoryApi.getTree()并处理成下拉列表格式
        categoryList.value = {
            0: '请选择分类',
        }
    } catch (error) {
        ElMessage.error('获取分类列表失败')
    }
}

// 处理搜索
const handleSearch = () => {
    pageInfo.page = 1
    loadGoodsList()
}

// 重置搜索
const resetSearch = () => {
    Object.assign(searchForm, {
        keyword: '',
        category_id: '',
        status: '',
    })
    pageInfo.page = 1
    loadGoodsList()
}

// 处理分页大小变化
const handleSizeChange = (size: number) => {
    pageInfo.limit = size
    loadGoodsList()
}

// 处理页码变化
const handleCurrentChange = (current: number) => {
    pageInfo.page = current
    loadGoodsList()
}

// 处理添加
const handleAdd = () => {
    dialogTitle.value = '添加商品'
    Object.assign(form, {
        id: 0,
        category_id: '',
        name: '',
        sn: '',
        price: 0,
        stock: 0,
        status: 1,
        description: '',
        attributes: [],
        specs: [],
    })
    dialogVisible.value = true
}

// 处理编辑
const handleEdit = (row: any) => {
    dialogTitle.value = '编辑商品'
    Object.assign(form, {
        id: row.id,
        category_id: row.category_id,
        name: row.name,
        sn: row.sn,
        price: row.price,
        stock: row.stock,
        status: row.status,
        description: row.description,
        attributes: row.attributes || [],
        specs: row.specs || [],
    })
    dialogVisible.value = true
}

// 处理删除
const handleDelete = async (id: number) => {
    try {
        const response = await goodsApi.delete(id)
        ElMessage.success(response.msg)
        loadGoodsList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '删除失败')
    }
}

// 处理状态切换
const handleToggleStatus = async (id: number) => {
    try {
        const response = await goodsApi.toggleStatus(id)
        ElMessage.success(response.msg)
        loadGoodsList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

// 处理提交
const handleSubmit = async () => {
    try {
        let response
        if (form.id === 0) {
            response = await goodsApi.add(form)
        } else {
            response = await goodsApi.edit(form.id, form)
        }
        ElMessage.success(response.msg)
        dialogVisible.value = false
        loadGoodsList()
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '操作失败')
    }
}

onMounted(() => {
    loadGoodsList()
    loadCategoryList()
})
</script>

<style scoped>
.app-container {
    padding: 20px;
}
</style>
