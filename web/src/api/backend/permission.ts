import createAxios from '/@/utils/axios'

const request = createAxios

// 数据权限管理
export const dataPermissionApi = {
    // 获取数据权限列表
    getList: (params: any) => {
        return request({
            url: '/admin/auth/dataPermission/index',
            method: 'get',
            params,
        })
    },
    // 添加数据权限
    add: (data: any) => {
        return request({
            url: '/admin/auth/dataPermission/add',
            method: 'post',
            data,
        })
    },
    // 编辑数据权限
    edit: (data: any) => {
        return request({
            url: '/admin/auth/dataPermission/edit',
            method: 'post',
            data,
        })
    },
    // 删除数据权限
    del: (ids: number[]) => {
        return request({
            url: '/admin/auth/dataPermission/del',
            method: 'post',
            data: { ids },
        })
    },
    // 获取角色列表
    getGroups: () => {
        return request({
            url: '/admin/auth/dataPermission/getGroups',
            method: 'get',
        })
    },
}

// 权限继承管理
export const permissionInheritApi = {
    // 获取权限继承列表
    getList: (params: any) => {
        return request({
            url: '/admin/auth/permissionInherit/index',
            method: 'get',
            params,
        })
    },
    // 添加权限继承
    add: (data: any) => {
        return request({
            url: '/admin/auth/permissionInherit/add',
            method: 'post',
            data,
        })
    },
    // 编辑权限继承
    edit: (data: any) => {
        return request({
            url: '/admin/auth/permissionInherit/edit',
            method: 'post',
            data,
        })
    },
    // 删除权限继承
    del: (ids: number[]) => {
        return request({
            url: '/admin/auth/permissionInherit/del',
            method: 'post',
            data: { ids },
        })
    },
    // 获取角色列表
    getGroups: () => {
        return request({
            url: '/admin/auth/permissionInherit/getGroups',
            method: 'get',
        })
    },
}
