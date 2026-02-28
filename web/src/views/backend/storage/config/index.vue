<template>
    <div class="storage-config">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>存储配置管理</span>
                    <el-button type="primary" @click="handleAdd">添加配置</el-button>
                </div>
            </template>

            <el-table :data="configList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="配置名称" />
                <el-table-column prop="type" label="存储类型" />
                <el-table-column prop="bucket" label="存储桶" />
                <el-table-column prop="region" label="区域" width="120" />
                <el-table-column prop="domain" label="访问域名" show-overflow-tooltip />
                <el-table-column prop="is_default" label="是否默认" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_default" type="success">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status" type="success">启用</el-tag>
                        <el-tag v-else type="danger">禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleEdit(row)">编辑</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                        <el-button v-if="!row.is_default" size="small" @click="handleSetDefault(row.id)">设为默认</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新增/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="配置名称">
                    <el-input v-model="form.name" placeholder="请输入配置名称" />
                </el-form-item>
                <el-form-item label="存储类型">
                    <el-select v-model="form.type" placeholder="请选择存储类型">
                        <el-option label="本地存储" value="local" />
                        <el-option label="阿里云OSS" value="oss" />
                        <el-option label="AWS S3" value="s3" />
                        <el-option label="七牛云" value="qiniu" />
                        <el-option label="腾讯云COS" value="cos" />
                    </el-select>
                </el-form-item>
                <el-form-item label="访问密钥">
                    <el-input v-model="form.access_key" placeholder="请输入访问密钥" />
                </el-form-item>
                <el-form-item label="Secret密钥">
                    <el-input v-model="form.secret_key" placeholder="请输入Secret密钥" />
                </el-form-item>
                <el-form-item label="存储桶">
                    <el-input v-model="form.bucket" placeholder="请输入存储桶" />
                </el-form-item>
                <el-form-item label="区域">
                    <el-input v-model="form.region" placeholder="请输入区域" />
                </el-form-item>
                <el-form-item label="端点">
                    <el-input v-model="form.endpoint" placeholder="请输入端点" />
                </el-form-item>
                <el-form-item label="访问域名">
                    <el-input v-model="form.domain" placeholder="请输入访问域名" />
                </el-form-item>
                <el-form-item label="路径前缀">
                    <el-input v-model="form.path_prefix" placeholder="请输入路径前缀" />
                </el-form-item>
                <el-form-item label="是否默认">
                    <el-switch v-model="form.is_default" />
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch v-model="form.status" />
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
import { ref, onMounted } from 'vue'
import { storageConfigApi } from '/@/api/backend/storage'
import { ElMessage } from 'element-plus'

// 类型定义
interface StorageConfig {
    id: number
    name: string
    type: string
    access_key: string
    secret_key: string
    bucket: string
    region: string
    endpoint: string
    domain: string
    path_prefix: string
    is_default: boolean
    status: boolean
}

// 数据
const configList = ref<StorageConfig[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('添加配置')
const form = ref({
    name: '',
    type: 'local',
    access_key: '',
    secret_key: '',
    bucket: '',
    region: '',
    endpoint: '',
    domain: '',
    path_prefix: '',
    is_default: false,
    status: true,
})
const currentId = ref(0)

// 生命周期
onMounted(() => {
    fetchConfigList()
})

// 方法
const fetchConfigList = async () => {
    try {
        const response = await storageConfigApi.getList()
        if (response.code === 0) {
            configList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取配置列表失败')
    }
}

const handleAdd = () => {
    dialogTitle.value = '添加配置'
    form.value = {
        name: '',
        type: 'local',
        access_key: '',
        secret_key: '',
        bucket: '',
        region: '',
        endpoint: '',
        domain: '',
        path_prefix: '',
        is_default: false,
        status: true,
    }
    currentId.value = 0
    dialogVisible.value = true
}

const handleEdit = (row: StorageConfig) => {
    dialogTitle.value = '编辑配置'
    form.value = { ...row }
    currentId.value = row.id
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        let response
        if (currentId.value) {
            response = await storageConfigApi.update(currentId.value, form.value)
        } else {
            response = await storageConfigApi.create(form.value)
        }
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await storageConfigApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}

const handleSetDefault = async (id: number) => {
    try {
        const response = await storageConfigApi.setDefault(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('设置失败')
    }
}
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
