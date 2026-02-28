<template>
    <div class="social-user">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>社交媒体用户管理</span>
                </div>
            </template>

            <el-table :data="userList" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="nickname" label="昵称" />
                <el-table-column prop="openid" label="OpenID" />
                <el-table-column prop="unionid" label="UnionID" />
                <el-table-column prop="config.platform" label="平台" width="100" />
                <el-table-column prop="user.username" label="绑定用户" />
                <el-table-column prop="create_time" label="创建时间" width="180">
                    <template #default="{ row }">
                        {{ row.create_time ? new Date(row.create_time * 1000).toLocaleString() : '-' }}
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="handleGetUserInfo(row.id)">查看信息</el-button>
                        <el-button v-if="!row.user_id" size="small" @click="handleBindUser(row.id)">绑定用户</el-button>
                        <el-button v-else size="small" @click="handleUnbindUser(row.id)">解绑用户</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 绑定用户对话框 -->
        <el-dialog v-model="bindDialogVisible" title="绑定系统用户" width="400px">
            <el-form :model="bindForm" label-width="80px">
                <el-form-item label="用户ID">
                    <el-input v-model.number="bindForm.user_id" placeholder="请输入系统用户ID" type="number" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="bindDialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="handleBindSubmit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <!-- 用户信息对话框 -->
        <el-dialog v-model="infoDialogVisible" title="用户信息" width="600px">
            <el-form :model="userInfo" label-width="120px">
                <el-form-item label="昵称">
                    <el-input v-model="userInfo.nickname" disabled />
                </el-form-item>
                <el-form-item label="头像">
                    <el-image :src="userInfo.avatar" style="width: 100px; height: 100px" />
                </el-form-item>
                <el-form-item label="OpenID">
                    <el-input v-model="userInfo.openid" disabled />
                </el-form-item>
                <el-form-item label="UnionID">
                    <el-input v-model="userInfo.unionid" disabled />
                </el-form-item>
                <el-form-item label="性别">
                    <el-input v-model="userInfo.gender" disabled />
                </el-form-item>
                <el-form-item label="国家">
                    <el-input v-model="userInfo.country" disabled />
                </el-form-item>
                <el-form-item label="省份">
                    <el-input v-model="userInfo.province" disabled />
                </el-form-item>
                <el-form-item label="城市">
                    <el-input v-model="userInfo.city" disabled />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="infoDialogVisible = false">关闭</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { socialUserApi } from '/@/api/backend/social'
import { ElMessage } from 'element-plus'

// 类型定义
interface SocialUser {
    id: number
    nickname: string
    openid: string
    unionid: string
    user_id?: number
    create_time?: number
    config?: {
        platform: string
    }
    user?: {
        username: string
    }
}

interface SocialUserInfo extends SocialUser {
    avatar: string
    gender: string
    country: string
    province: string
    city: string
}

// 数据
const userList = ref<SocialUser[]>([])
const bindDialogVisible = ref(false)
const infoDialogVisible = ref(false)
const bindForm = ref({ user_id: 0 })
const userInfo = ref<SocialUserInfo>({
    id: 0,
    nickname: '',
    openid: '',
    unionid: '',
    avatar: '',
    gender: '',
    country: '',
    province: '',
    city: '',
})
const currentUserId = ref(0)

// 生命周期
onMounted(() => {
    fetchUserList()
})

// 方法
const fetchUserList = async () => {
    try {
        const response = await socialUserApi.getList()
        if (response.code === 0) {
            userList.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取用户列表失败')
    }
}

const handleBindUser = (id: number) => {
    currentUserId.value = id
    bindForm.value = { user_id: 0 }
    bindDialogVisible.value = true
}

const handleBindSubmit = async () => {
    if (!bindForm.value.user_id) {
        ElMessage.warning('请输入用户ID')
        return
    }
    try {
        const response = await socialUserApi.bindUser(currentUserId.value, bindForm.value.user_id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            bindDialogVisible.value = false
            fetchUserList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('绑定失败')
    }
}

const handleUnbindUser = async (id: number) => {
    try {
        const response = await socialUserApi.unbindUser(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchUserList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('解绑失败')
    }
}

const handleGetUserInfo = async (id: number) => {
    try {
        const response = await socialUserApi.getUserInfo(id)
        if (response.code === 0) {
            userInfo.value = response.data
            infoDialogVisible.value = true
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('获取用户信息失败')
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
