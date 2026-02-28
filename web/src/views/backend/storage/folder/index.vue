<template>
    <div class="storage-folder">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>文件夹管理</span>
                    <el-button type="primary" @click="handleAdd">创建文件夹</el-button>
                </div>
            </template>

            <el-tree :data="folderTree" node-key="id" :props="defaultProps" @node-click="handleNodeClick" @node-contextmenu="handleNodeContextMenu">
                <template #default="{ node, data }">
                    <span class="custom-tree-node">
                        <span>{{ node.label }}</span>
                        <span>
                            <el-button type="text" size="small" @click.stop="handleEdit(data)"> 编辑 </el-button>
                            <el-button type="text" size="small" @click.stop="handleDelete(data.id)"> 删除 </el-button>
                        </span>
                    </span>
                </template>
            </el-tree>
        </el-card>

        <!-- 新增/编辑对话框 -->
        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="400px">
            <el-form :model="form" label-width="80px">
                <el-form-item label="文件夹名称">
                    <el-input v-model="form.name" placeholder="请输入文件夹名称" />
                </el-form-item>
                <el-form-item label="父文件夹">
                    <el-select v-model="form.parent_id" placeholder="请选择父文件夹">
                        <el-option label="根目录" value="0" />
                        <el-option v-for="folder in folderList" :key="folder.id" :label="folder.path" :value="folder.id" />
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
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { storageFolderApi } from '/@/api/backend/storage'
import { ElMessage } from 'element-plus'

// 类型定义
interface Folder {
    id: number
    name: string
    parent_id: number
    path: string
    children?: Folder[]
}

// 数据
const folderTree = ref<Folder[]>([])
const folderList = ref<Folder[]>([])
const dialogVisible = ref(false)
const dialogTitle = ref('创建文件夹')
const form = ref({
    name: '',
    parent_id: 0,
})
const currentId = ref(0)

// 树形配置
const defaultProps = {
    children: 'children',
    label: 'name',
}

// 生命周期
onMounted(() => {
    fetchFolderTree()
    fetchFolderList()
})

// 方法
const fetchFolderTree = async () => {
    try {
        const response = await storageFolderApi.getTree()
        if (response.code === 0) {
            folderTree.value = response.data
        }
    } catch (error) {
        ElMessage.error('获取文件夹树失败')
    }
}

const fetchFolderList = async () => {
    try {
        const response = await storageFolderApi.getList()
        if (response.code === 0) {
            // 扁平化文件夹列表
            const flattenFolders = (folders: Folder[]): Folder[] => {
                let result: Folder[] = []
                folders.forEach((folder: Folder) => {
                    result.push(folder)
                    if (folder.children && folder.children.length > 0) {
                        result = result.concat(flattenFolders(folder.children))
                    }
                })
                return result
            }
            folderList.value = flattenFolders(response.data)
        }
    } catch (error) {
        ElMessage.error('获取文件夹列表失败')
    }
}

const handleNodeClick = (data: Folder) => {
    console.log('点击节点:', data)
}

const handleNodeContextMenu = (event: MouseEvent, data: Folder) => {
    event.preventDefault()
    console.log('右键菜单:', data)
}

const handleAdd = () => {
    dialogTitle.value = '创建文件夹'
    form.value = {
        name: '',
        parent_id: 0,
    }
    currentId.value = 0
    dialogVisible.value = true
}

const handleEdit = (data: Folder) => {
    dialogTitle.value = '编辑文件夹'
    form.value = {
        name: data.name,
        parent_id: data.parent_id,
    }
    currentId.value = data.id
    dialogVisible.value = true
}

const handleSubmit = async () => {
    try {
        let response
        if (currentId.value) {
            response = await storageFolderApi.update(currentId.value, form.value)
        } else {
            response = await storageFolderApi.create(form.value)
        }
        if (response.code === 0) {
            ElMessage.success(response.msg)
            dialogVisible.value = false
            fetchFolderTree()
            fetchFolderList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('操作失败')
    }
}

const handleDelete = async (id: number) => {
    try {
        const response = await storageFolderApi.delete(id)
        if (response.code === 0) {
            ElMessage.success(response.msg)
            fetchFolderTree()
            fetchFolderList()
        } else {
            ElMessage.error(response.msg)
        }
    } catch (error) {
        ElMessage.error('删除失败')
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

.custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    padding-right: 8px;
}
</style>
