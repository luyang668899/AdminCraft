<template>
    <div class="social-config">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>社交媒体配置管理</span>
                    <el-button type="primary" @click="handleAdd">添加配置</el-button>
                </div>
            </template>

            <el-table :data="configList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="name" label="配置名称" />
                <el-table-column prop="platform" label="平台类型" />
                <el-table-column prop="app_id" label="应用ID" />
                <el-table-column prop="redirect_uri" label="回调地址" show-overflow-tooltip />
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
                        <el-button size="small" @click="handleRefreshToken(row.id)">刷新令牌</el-button>
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
                <el-form-item label="平台类型">
                    <el-select v-model="form.platform" placeholder="请选择平台类型">
                        <el-option label="微信" value="wechat" />
                        <el-option label="微博" value="weibo" />
                        <el-option label="抖音" value="douyin" />
                        <el-option label="QQ" value="qq" />
                        <el-option label="Facebook" value="facebook" />
                        <el-option label="Twitter" value="twitter" />
                    </el-select>
                </el-form-item>
                <el-form-item label="应用ID">
                    <el-input v-model="form.app_id" placeholder="请输入应用ID" />
                </el-form-item>
                <el-form-item label="应用密钥">
                    <el-input v-model="form.app_secret" placeholder="请输入应用密钥" />
                </el-form-item>
                <el-form-item label="回调地址">
                    <el-input v-model="form.redirect_uri" placeholder="请输入回调地址" />
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
import { socialConfigApi } from '/@/api/backend/social'
import { ElMessage } from 'element-plus'

// 类型定义
interface SocialConfig {
    id: number
    name: string
    platform: string
    app_id: string
    app_secret: string
    redirect_uri: string
    status: boolean
}

// 数据
const configList = ref<SocialConfig[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('添加配置')
const form = ref({
    name: '',
    platform: 'wechat',
    app_id: '',
    app_secret: '',
    redirect_uri: '',
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
        const response = await socialConfigApi.getList()
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
        platform: 'wechat',
        app_id: '',
        app_secret: '',
        redirect_uri: '',
        status: true,
    }
    currentId.value = 0
    dialogVisible.value = true
}

const handleEdit = (row: SocialConfig) => {
    dialogTitle.value = '编辑配置'
    form.value = { ...row }
    currentId.value = row.id
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        let response
        if (currentId.value) {
            response = await socialConfigApi.update(currentId.value, form.value)
        } else {
            response = await socialConfigApi.create(form.value)
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
        const response = await socialConfigApi.delete(id)
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

const handleRefreshToken = async (id: number) => {
    try {
        const response = await socialConfigApi.refreshToken(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchConfigList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('刷新令牌失败')
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
