<template>
    <div class="app-container">
        <el-card class="box-card">
            <template #header>
                <div class="card-header">
                    <span>插件管理</span>
                    <el-button type="primary" @click="refreshPlugins" size="small">
                        <el-icon><Refresh /></el-icon>
                        刷新
                    </el-button>
                </div>
            </template>

            <el-table v-loading="loading" :data="dataList" style="width: 100%" border>
                <el-table-column prop="name" label="插件名称" width="150" />
                <el-table-column prop="title" label="插件标题" width="200" />
                <el-table-column prop="description" label="插件描述" />
                <el-table-column prop="version" label="版本" width="100" />
                <el-table-column prop="author" label="作者" width="120" />
                <el-table-column prop="status" label="状态" width="120">
                    <template #default="scope">
                        <el-tag :type="getStatusTagType(scope.row.status)">
                            {{ getStatusText(scope.row.status) }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="300" fixed="right">
                    <template #default="scope">
                        <template v-if="scope.row.status === 0">
                            <el-button type="primary" size="small" @click="handleInstall(scope.row.name)"> 安装 </el-button>
                        </template>
                        <template v-else-if="scope.row.status === 1">
                            <el-button type="primary" size="small" @click="handleEnable(scope.row.name)"> 启用 </el-button>
                            <el-button type="danger" size="small" @click="handleUninstall(scope.row.name)"> 卸载 </el-button>
                        </template>
                        <template v-else-if="scope.row.status === 2">
                            <el-button type="warning" size="small" @click="handleDisable(scope.row.name)"> 禁用 </el-button>
                            <el-button type="info" size="small" @click="handleConfig(scope.row.name, scope.row.title)"> 配置 </el-button>
                            <el-button type="danger" size="small" @click="handleUninstall(scope.row.name)"> 卸载 </el-button>
                        </template>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 插件配置对话框 -->
        <el-dialog v-model="configDialogVisible" :title="`${configPluginTitle} - 配置`" width="600px">
            <el-form :model="pluginConfig" ref="configFormRef" label-width="120px">
                <el-form-item label="配置内容">
                    <el-input v-model="configJson" type="textarea" :rows="10" placeholder="请输入JSON格式的配置" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="configDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSaveConfig">保存</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Refresh } from '@element-plus/icons-vue'
import { pluginApi } from '/@/api/backend/plugin'

// 状态
const loading = ref(false)
const configDialogVisible = ref(false)
const configFormRef = ref()

// 数据
const dataList = ref([])

// 配置
const configPluginName = ref('')
const configPluginTitle = ref('')
const pluginConfig = reactive({})
const configJson = ref('')

// 初始化
onMounted(() => {
    getPluginList()
})

// 获取插件列表
const getPluginList = async () => {
    loading.value = true
    try {
        const res = await pluginApi.getPluginList()
        if (res.code === 1) {
            dataList.value = res.data.plugins
        }
    } catch (error) {
        ElMessage.error('获取插件列表失败')
    } finally {
        loading.value = false
    }
}

// 刷新插件列表
const refreshPlugins = () => {
    getPluginList()
}

// 安装插件
const handleInstall = async (name: string) => {
    ElMessageBox.confirm('确定要安装这个插件吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
    }).then(async () => {
        try {
            const res = await pluginApi.install(name)
            if (res.code === 1) {
                ElMessage.success('安装成功')
                getPluginList()
            }
        } catch (error: any) {
            ElMessage.error(error.response?.data?.msg || '安装失败')
        }
    })
}

// 卸载插件
const handleUninstall = async (name: string) => {
    ElMessageBox.confirm('确定要卸载这个插件吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
    }).then(async () => {
        try {
            const res = await pluginApi.uninstall(name)
            if (res.code === 1) {
                ElMessage.success('卸载成功')
                getPluginList()
            }
        } catch (error: any) {
            ElMessage.error(error.response?.data?.msg || '卸载失败')
        }
    })
}

// 启用插件
const handleEnable = async (name: string) => {
    try {
        const res = await pluginApi.enable(name)
        if (res.code === 1) {
            ElMessage.success('启用成功')
            getPluginList()
        }
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '启用失败')
    }
}

// 禁用插件
const handleDisable = async (name: string) => {
    try {
        const res = await pluginApi.disable(name)
        if (res.code === 1) {
            ElMessage.success('禁用成功')
            getPluginList()
        }
    } catch (error: any) {
        ElMessage.error(error.response?.data?.msg || '禁用失败')
    }
}

// 配置插件
const handleConfig = async (name: string, title: string) => {
    configPluginName.value = name
    configPluginTitle.value = title

    try {
        const res = await pluginApi.getConfig(name)
        if (res.code === 1) {
            Object.assign(pluginConfig, res.data.config)
            configJson.value = JSON.stringify(res.data.config, null, 2)
            configDialogVisible.value = true
        }
    } catch (error) {
        ElMessage.error('获取配置失败')
    }
}

// 保存配置
const handleSaveConfig = async () => {
    try {
        const config = JSON.parse(configJson.value)
        const res = await pluginApi.setConfig(configPluginName.value, config)
        if (res.code === 1) {
            ElMessage.success('配置保存成功')
            configDialogVisible.value = false
        }
    } catch (error) {
        ElMessage.error('配置格式错误，请检查JSON格式')
    }
}

// 获取状态标签类型
const getStatusTagType = (status: number): 'success' | 'warning' | 'info' | 'primary' | 'danger' => {
    const types: Record<number, 'success' | 'warning' | 'info' | 'primary' | 'danger'> = {
        0: 'info',
        1: 'warning',
        2: 'success',
    }
    return types[status] || 'info'
}

// 获取状态文本
const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        0: '未安装',
        1: '已安装',
        2: '已启用',
    }
    return texts[status] || '未知'
}
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
