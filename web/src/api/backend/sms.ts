import createAxios from '/@/utils/axios'

const request = createAxios

// 短信配置API
export const smsConfigApi = {
    // 获取短信配置列表
    getList: (params: any) => {
        return request({
            url: '/admin/sms/config/list',
            method: 'get',
            params,
        })
    },

    // 创建短信配置
    create: (data: any) => {
        return request({
            url: '/admin/sms/config/save',
            method: 'post',
            data,
        })
    },

    // 更新短信配置
    update: (data: any) => {
        return request({
            url: '/admin/sms/config/update',
            method: 'post',
            data,
        })
    },

    // 删除短信配置
    delete: (id: number) => {
        return request({
            url: `/admin/sms/config/delete/${id}`,
            method: 'get',
        })
    },

    // 切换短信配置状态
    toggleStatus: (id: number) => {
        return request({
            url: `/admin/sms/config/toggleStatus/${id}`,
            method: 'get',
        })
    },
}

// 短信记录API
export const smsRecordApi = {
    // 获取短信记录列表
    getList: (params: any) => {
        return request({
            url: '/admin/sms/record/list',
            method: 'get',
            params,
        })
    },

    // 删除短信记录
    delete: (id: number) => {
        return request({
            url: `/admin/sms/record/delete/${id}`,
            method: 'get',
        })
    },

    // 发送短信
    send: (data: any) => {
        return request({
            url: '/admin/sms/record/send',
            method: 'post',
            data,
        })
    },
}

// 短信验证码API
export const smsVerificationApi = {
    // 获取短信验证码列表
    getList: (params: any) => {
        return request({
            url: '/admin/sms/verification/list',
            method: 'get',
            params,
        })
    },

    // 删除短信验证码
    delete: (id: number) => {
        return request({
            url: `/admin/sms/verification/delete/${id}`,
            method: 'get',
        })
    },

    // 发送验证码
    sendCode: (data: any) => {
        return request({
            url: '/admin/sms/verification/sendCode',
            method: 'post',
            data,
        })
    },

    // 验证验证码
    verifyCode: (data: any) => {
        return request({
            url: '/admin/sms/verification/verifyCode',
            method: 'post',
            data,
        })
    },
}
