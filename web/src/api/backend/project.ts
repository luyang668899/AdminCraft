import createAxios from '/@/utils/axios'

const request = createAxios

// 项目管理API
export const projectApi = {
    // 获取项目列表
    getList: (params: any) => {
        return request({
            url: '/admin/project/project/list',
            method: 'get',
            params,
        })
    },

    // 创建项目
    create: (data: any) => {
        return request({
            url: '/admin/project/project/save',
            method: 'post',
            data,
        })
    },

    // 更新项目
    update: (data: any) => {
        return request({
            url: '/admin/project/project/update',
            method: 'post',
            data,
        })
    },

    // 删除项目
    delete: (id: number) => {
        return request({
            url: `/admin/project/project/delete/${id}`,
            method: 'get',
        })
    },

    // 获取项目成员列表
    getMembers: (projectId: number) => {
        return request({
            url: `/admin/project/project/members/${projectId}`,
            method: 'get',
        })
    },

    // 添加项目成员
    addMember: (data: any) => {
        return request({
            url: '/admin/project/project/addMember',
            method: 'post',
            data,
        })
    },

    // 移除项目成员
    removeMember: (id: number) => {
        return request({
            url: `/admin/project/project/removeMember/${id}`,
            method: 'get',
        })
    },
}

// 任务管理API
export const taskApi = {
    // 获取任务列表
    getList: (params: any) => {
        return request({
            url: '/admin/project/task/list',
            method: 'get',
            params,
        })
    },

    // 创建任务
    create: (data: any) => {
        return request({
            url: '/admin/project/task/save',
            method: 'post',
            data,
        })
    },

    // 更新任务
    update: (data: any) => {
        return request({
            url: '/admin/project/task/update',
            method: 'post',
            data,
        })
    },

    // 删除任务
    delete: (id: number) => {
        return request({
            url: `/admin/project/task/delete/${id}`,
            method: 'get',
        })
    },

    // 更新任务状态
    updateStatus: (data: any) => {
        return request({
            url: '/admin/project/task/updateStatus',
            method: 'post',
            data,
        })
    },
}
