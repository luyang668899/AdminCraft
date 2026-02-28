import createAxios from '/@/utils/axios'

const request = createAxios

// 支付配置API
export const paymentConfigApi = {
    // 获取支付配置列表
    getList: (params: any) => {
        return request({
            url: '/admin/payment/config/list',
            method: 'get',
            params,
        })
    },

    // 创建支付配置
    create: (data: any) => {
        return request({
            url: '/admin/payment/config/save',
            method: 'post',
            data,
        })
    },

    // 更新支付配置
    update: (data: any) => {
        return request({
            url: '/admin/payment/config/update',
            method: 'post',
            data,
        })
    },

    // 删除支付配置
    delete: (id: number) => {
        return request({
            url: `/admin/payment/config/delete/${id}`,
            method: 'get',
        })
    },

    // 切换支付配置状态
    toggleStatus: (id: number) => {
        return request({
            url: `/admin/payment/config/toggleStatus/${id}`,
            method: 'get',
        })
    },
}

// 支付记录API
export const paymentRecordApi = {
    // 获取支付记录列表
    getList: (params: any) => {
        return request({
            url: '/admin/payment/record/list',
            method: 'get',
            params,
        })
    },

    // 删除支付记录
    delete: (id: number) => {
        return request({
            url: `/admin/payment/record/delete/${id}`,
            method: 'get',
        })
    },

    // 获取支付记录日志
    getLogs: (recordId: number) => {
        return request({
            url: `/admin/payment/record/logs/${recordId}`,
            method: 'get',
        })
    },
}
