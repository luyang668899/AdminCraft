<template>
    <div class="social-share">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>社交媒体分享管理</span>
                    <el-button type="primary" @click="handleAdd">创建分享</el-button>
                    <el-button type="success" @click="handleGetStats">查看统计</el-button>
                </div>
            </template>

            <el-table :data="shareList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="content" label="分享内容" show-overflow-tooltip />
                <el-table-column prop="url" label="分享链接" show-overflow-tooltip />
                <el-table-column prop="platform" label="分享平台" width="120" />
                <el-table-column prop="user.username" label="分享用户" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="info">待分享</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">分享成功</el-tag>
                        <el-tag v-else type="danger">分享失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="share_time" label="分享时间" width="180">
                    <template #default="{ row }">
                        {{ row.share_time ? new Date(row.share_time * 1000).toLocaleString() : '-' }}
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleDoShare(row.id)">执行分享</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新增分享对话框 -->
        <el-dialog v-model="dialogVisible" title="创建分享" width="600px">
            <el-form :model="form" label-width="120px">
                <el-form-item label="配置ID">
                    <el-input v-model.number="form.config_id" placeholder="请输入配置ID" type="number" />
                </el-form-item>
                <el-form-item label="用户ID">
                    <el-input v-model.number="form.user_id" placeholder="请输入用户ID" type="number" />
                </el-form-item>
                <el-form-item label="分享内容">
                    <el-input v-model="form.content" type="textarea" placeholder="请输入分享内容" :rows="3" />
                </el-form-item>
                <el-form-item label="分享链接">
                    <el-input v-model="form.url" placeholder="请输入分享链接" />
                </el-form-item>
                <el-form-item label="分享平台">
                    <el-select v-model="form.platform" placeholder="请选择分享平台">
                        <el-option label="微信" value="wechat" />
                        <el-option label="微博" value="weibo" />
                        <el-option label="抖音" value="douyin" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 统计对话框 -->
        <el-dialog v-model="statsDialogVisible" title="分享统计" width="600px">
            <el-form :model="statsForm" label-width="120px">
                <el-form-item label="平台">
                    <el-select v-model="statsForm.platform" placeholder="请选择平台">
                        <el-option label="全部" value="" />
                        <el-option label="微信" value="wechat" />
                        <el-option label="微博" value="weibo" />
                        <el-option label="抖音" value="douyin" />
                    </el-select>
                </el-form-item>
                <el-form-item label="开始时间">
                    <el-date-picker v-model="statsForm.start_time" type="datetime" placeholder="请选择开始时间" style="width: 100%" />
                </el-form-item>
                <el-form-item label="结束时间">
                    <el-date-picker v-model="statsForm.end_time" type="datetime" placeholder="请选择结束时间" style="width: 100%" />
                </el-form-item>
            </el-form>
            <el-button type="primary" @click="fetchStats">查询统计</el-button>

            <el-card v-if="statsData" class="mt-4">
                <template #header>
                    <div class="card-header">
                        <span>统计结果</span>
                    </div>
                </template>
                <div class="stats-content">
                    <div class="total">
                        <h3>总分享数: {{ statsData.total }}</h3>
                    </div>
                    <div class="platforms" v-if="statsData.platforms && statsData.platforms.length > 0">
                        <h4>平台分布:</h4>
                        <el-table :data="statsData.platforms" style="width: 100%">
                            <el-table-column prop="platform" label="平台" />
                            <el-table-column prop="count" label="分享数" />
                        </el-table>
                    </div>
                </div>
            </el-card>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="statsDialogVisible = false">关闭</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { socialShareApi } from '/@/api/backend/social'
import { ElMessage } from 'element-plus'

// 类型定义
interface Share {
    id: number
    config_id: number
    user_id: number
    content: string
    url: string
    platform: string
    status: number
    share_time?: number
    user?: {
        username: string
    }
}

interface PlatformStats {
    platform: string
    count: number
}

interface ShareStats {
    total: number
    platforms: PlatformStats[]
}

// 数据
const shareList = ref<Share[]>([])
const dialogVisible = ref(false)
const statsDialogVisible = ref(false)
const form = ref({
    config_id: 0,
    user_id: 0,
    content: '',
    url: '',
    platform: 'wechat',
})
const statsForm = ref({
    platform: '',
    start_time: '',
    end_time: '',
})
const statsData = ref<ShareStats | null>(null)

// 生命周期
onMounted(() => {
    fetchShareList()
})

// 方法
const fetchShareList = async () => {
    try {
        const response = await socialShareApi.getList()
        if (response.code === 0) {
            shareList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取分享列表失败')
    }
}

const handleAdd = () => {
    form.value = {
        config_id: 0,
        user_id: 0,
        content: '',
        url: '',
        platform: 'wechat',
    }
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        const response = await socialShareApi.create(form.value)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchShareList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('创建失败')
    }
}

const handleDoShare = async (id: number) => {
    try {
        const response = await socialShareApi.doShare(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchShareList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('执行分享失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await socialShareApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchShareList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
    }
}

const handleGetStats = () => {
    statsForm.value = {
        platform: '',
        start_time: '',
        end_time: '',
    }
    statsData.value = null
    statsDialogVisible.value = true
}

const fetchStats = async () => {
    try {
        const params = {
            platform: statsForm.value.platform,
            start_time: statsForm.value.start_time ? Math.floor(new Date(statsForm.value.start_time).getTime() / 1000) : '',
            end_time: statsForm.value.end_time ? Math.floor(new Date(statsForm.value.end_time).getTime() / 1000) : '',
        }
        const response = await socialShareApi.getStats(params)
        if (response.code === 0) {
            statsData.value = response.data
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('获取统计失败')
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

.mt-4 {
    margin-top: 16px;
}

.stats-content {
    padding: 16px;
}

.total {
    margin-bottom: 16px;
}

.platforms {
    margin-top: 16px;
}
</style>
