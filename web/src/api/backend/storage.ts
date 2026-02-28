import createAxios from '/@/utils/axios'

const request = createAxios

// 存储配置相关API
export const storageConfigApi = {
    // 获取存储配置列表
    getList: () =>
        request({
            url: '/admin/storage/config/index',
            method: 'get',
        }),

    // 获取单个存储配置
    getOne: (id: number) =>
        request({
            url: `/admin/storage/config/read/${id}`,
            method: 'get',
        }),

    // 创建存储配置
    create: (data: any) =>
        request({
            url: '/admin/storage/config/save',
            method: 'post',
            data,
        }),

    // 更新存储配置
    update: (id: number, data: any) =>
        request({
            url: `/admin/storage/config/update/${id}`,
            method: 'post',
            data,
        }),

    // 删除存储配置
    delete: (id: number) =>
        request({
            url: `/admin/storage/config/delete/${id}`,
            method: 'post',
        }),

    // 设置默认配置
    setDefault: (id: number) =>
        request({
            url: `/admin/storage/config/setDefault/${id}`,
            method: 'post',
        }),
}

// 文件管理相关API
export const storageFileApi = {
    // 获取文件列表
    getList: () =>
        request({
            url: '/admin/storage/file/index',
            method: 'get',
        }),

    // 上传文件
    upload: (formData: FormData) =>
        request({
            url: '/admin/storage/file/upload',
            method: 'post',
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }),

    // 获取单个文件
    getOne: (id: number) =>
        request({
            url: `/admin/storage/file/read/${id}`,
            method: 'get',
        }),

    // 删除文件
    delete: (id: number) =>
        request({
            url: `/admin/storage/file/delete/${id}`,
            method: 'post',
        }),

    // 批量删除文件
    batchDelete: (ids: number[]) =>
        request({
            url: '/admin/storage/file/batchDelete',
            method: 'post',
            data: { ids },
        }),

    // 下载文件
    download: (id: number) =>
        request({
            url: `/admin/storage/file/download/${id}`,
            method: 'get',
        }),
}

// 文件夹管理相关API
export const storageFolderApi = {
    // 获取文件夹列表
    getList: () =>
        request({
            url: '/admin/storage/folder/index',
            method: 'get',
        }),

    // 创建文件夹
    create: (data: any) =>
        request({
            url: '/admin/storage/folder/create',
            method: 'post',
            data,
        }),

    // 获取单个文件夹
    getOne: (id: number) =>
        request({
            url: `/admin/storage/folder/read/${id}`,
            method: 'get',
        }),

    // 更新文件夹
    update: (id: number, data: any) =>
        request({
            url: `/admin/storage/folder/update/${id}`,
            method: 'post',
            data,
        }),

    // 删除文件夹
    delete: (id: number) =>
        request({
            url: `/admin/storage/folder/delete/${id}`,
            method: 'post',
        }),

    // 获取文件夹树
    getTree: () =>
        request({
            url: '/admin/storage/folder/tree',
            method: 'get',
        }),
}

export default {
    storageConfigApi,
    storageFileApi,
    storageFolderApi,
}
