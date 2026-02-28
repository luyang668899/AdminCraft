<template>
    <div class="storage-file">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>文件管理</span>
                    <el-upload
                        class="upload-demo"
                        :action="uploadUrl"
                        :on-success="handleUploadSuccess"
                        :on-error="handleUploadError"
                        :before-upload="beforeUpload"
                        :data="{ config_id: configId }"
                        multiple
                    >
                        <el-button type="primary">上传文件</el-button>
                    </el-upload>
                    <el-button type="danger" @click="handleBatchDelete" :disabled="selectedIds.length === 0">批量删除</el-button>
                </div>
            </template>

            <el-table :data="fileList" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="55" />
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="original_name" label="文件名" />
                <el-table-column prop="size" label="大小" width="120">
                    <template #default="{ row }">
                        {{ formatFileSize(row.size) }}
                    </template>
                </el-table-column>
                <el-table-column prop="mime_type" label="类型" width="120" />
                <el-table-column prop="config.name" label="存储配置" width="120" />
                <el-table-column prop="upload_time" label="上传时间" width="180">
                    <template #default="{ row }">
                        {{ row.upload_time ? new Date(row.upload_time * 1000).toLocaleString() : '-' }}
                    </template>
                </el-table-column>
                <el-table-column prop="url" label="访问链接" show-overflow-tooltip />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleDownload(row.id)">下载</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 存储配置选择 -->
        <el-dialog v-model="configDialogVisible" title="选择存储配置" width="400px">
            <el-form :model="configForm" label-width="80px">
                <el-form-item label="存储配置">
                    <el-select v-model="configId" placeholder="请选择存储配置">
                        <el-option v-for="config in configList" :key="config.id" :label="config.name" :value="config.id" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="configDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="configDialogVisible = false">确定</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { storageFileApi, storageConfigApi } from '/@/api/backend/storage'
import { ElMessage } from 'element-plus'
import type { UploadRawFile } from 'element-plus'

// 类型定义
interface StorageConfig {
    id: number
    name: string
    is_default: boolean
}

interface File {
    id: number
    original_name: string
    size: number
    mime_type: string
    url: string
    upload_time?: number
    config?: {
        name: string
    }
}

// 数据
const fileList = ref<File[]>([])
const configList = ref<StorageConfig[]>([])
const selectedIds = ref<number[]>([])
const configId = ref(0)
const configDialogVisible = ref(false)
const configForm = ref({})

// 计算属性
const uploadUrl = computed(() => {
    return import.meta.env.VITE_BACKEND_API_BASE_URL + '/admin/storage/file/upload'
})

// 生命周期
onMounted(() => {
    fetchFileList()
    fetchConfigList()
})

// 方法
const fetchFileList = async () => {
    try {
        const response = await storageFileApi.getList()
        if (response.code === 0) {
            fileList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取文件列表失败')
    }
}

const fetchConfigList = async () => {
    try {
        const response = await storageConfigApi.getList()
        if (response.code === 0) {
            configList.value = response.data
            // 设置默认配置
            const defaultConfig = response.data.find((config: StorageConfig) => config.is_default)
            if (defaultConfig) {
                configId.value = defaultConfig.id
            }
        }
    } catch (error) {
        ElMessage.error('获取配置列表失败')
    }
}

const handleSelectionChange = (selection: File[]) => {
    selectedIds.value = selection.map((item: File) => item.id)
}

const handleUploadSuccess = (response: any) => {
    if (response.code === 0) {
        ElMessage.success('上传成功')
        fetchFileList()
    } else {
        ElMessage.error(response.msg)
    }
}

const handleUploadError = () => {
    ElMessage.error('上传失败')
}

const beforeUpload = (file: UploadRawFile) => {
    // 可以在这里添加文件验证逻辑
    return true
}

const handleDelete = async (id: number) => {
    try {
        const response = await storageFileApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchFileList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}

const handleBatchDelete = async () => {
    if (selectedIds.value.length === 0) {
        ElMessage.warning('请选择要删除的文件')
        return
    }
    try {
        const response = await storageFileApi.batchDelete(selectedIds.value)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchFileList()
            selectedIds.value = []
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('批量删除失败')
    }
}

const handleDownload = async (id: number) => {
    try {
        const response = await storageFileApi.download(id)
        if (response.code === 0) {
            // 打开下载链接
            window.open(response.data.url, '_blank')
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('下载失败')
    }
}

const formatFileSize = (size: number) => {
    if (size < 1024) {
        return size + ' B'
    } else if (size < 1024 * 1024) {
        return (size / 1024).toFixed(2) + ' KB'
    } else if (size < 1024 * 1024 * 1024) {
        return (size / (1024 * 1024)).toFixed(2) + ' MB'
    } else {
        return (size / (1024 * 1024 * 1024)).toFixed(2) + ' GB'
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

.upload-demo {
    margin-right: 10px;
}
</style>
