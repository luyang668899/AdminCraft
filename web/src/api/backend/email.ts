import createAxios from '/@/utils/axios'

const request = createAxios

// 邮件配置相关API
export const emailConfigApi = {
    // 获取邮件配置列表
    getList: () =>
        request({
            url: '/admin/email/config/index',
            method: 'get',
        }),

    // 获取单个邮件配置
    getOne: (id: number) =>
        request({
            url: `/admin/email/config/read/${id}`,
            method: 'get',
        }),

    // 创建邮件配置
    create: (data: any) =>
        request({
            url: '/admin/email/config/save',
            method: 'post',
            data,
        }),

    // 更新邮件配置
    update: (id: number, data: any) =>
        request({
            url: `/admin/email/config/update/${id}`,
            method: 'post',
            data,
        }),

    // 删除邮件配置
    delete: (id: number) =>
        request({
            url: `/admin/email/config/delete/${id}`,
            method: 'post',
        }),

    // 设置默认配置
    setDefault: (id: number) =>
        request({
            url: `/admin/email/config/setDefault/${id}`,
            method: 'post',
        }),
}

// 邮件模板相关API
export const emailTemplateApi = {
    // 获取邮件模板列表
    getList: () =>
        request({
            url: '/admin/email/template/index',
            method: 'get',
        }),

    // 获取单个邮件模板
    getOne: (id: number) =>
        request({
            url: `/admin/email/template/read/${id}`,
            method: 'get',
        }),

    // 创建邮件模板
    create: (data: any) =>
        request({
            url: '/admin/email/template/save',
            method: 'post',
            data,
        }),

    // 更新邮件模板
    update: (id: number, data: any) =>
        request({
            url: `/admin/email/template/update/${id}`,
            method: 'post',
            data,
        }),

    // 删除邮件模板
    delete: (id: number) =>
        request({
            url: `/admin/email/template/delete/${id}`,
            method: 'post',
        }),
}

// 邮件发送记录相关API
export const emailRecordApi = {
    // 获取邮件发送记录列表
    getList: () =>
        request({
            url: '/admin/email/record/index',
            method: 'get',
        }),

    // 获取单个邮件发送记录
    getOne: (id: number) =>
        request({
            url: `/admin/email/record/read/${id}`,
            method: 'get',
        }),

    // 创建邮件发送记录
    create: (data: any) =>
        request({
            url: '/admin/email/record/save',
            method: 'post',
            data,
        }),

    // 重新发送邮件
    resend: (id: number) =>
        request({
            url: `/admin/email/record/resend/${id}`,
            method: 'post',
        }),

    // 批量发送邮件
    batchSend: (data: any) =>
        request({
            url: '/admin/email/record/batchSend',
            method: 'post',
            data,
        }),

    // 删除邮件发送记录
    delete: (id: number) =>
        request({
            url: `/admin/email/record/delete/${id}`,
            method: 'post',
        }),
}

export default {
    emailConfigApi,
    emailTemplateApi,
    emailRecordApi,
}
