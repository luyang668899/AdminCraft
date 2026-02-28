import createAxios from '/@/utils/axios'

const request = createAxios

// 客户相关API
export const customerApi = {
    // 获取客户列表
    getList: (params: any) => request({ url: '/admin/crm/customer/index', method: 'get', params }),
    // 添加客户
    add: (data: any) => request({ url: '/admin/crm/customer/add', method: 'post', data }),
    // 编辑客户
    edit: (id: number, data: any) => request({ url: `/admin/crm/customer/edit/${id}`, method: 'post', data }),
    // 删除客户
    delete: (id: number) => request({ url: `/admin/crm/customer/delete/${id}`, method: 'post' }),
    // 获取跟进记录列表
    getFollowList: (customer_id: number, params: any) => request({ url: `/admin/crm/customer/followList/${customer_id}`, method: 'get', params }),
    // 添加跟进记录
    addFollow: (data: any) => request({ url: '/admin/crm/customer/addFollow', method: 'post', data }),
}

// 销售漏斗相关API
export const salesFunnelApi = {
    // 获取销售漏斗列表
    getList: (params: any) => request({ url: '/admin/crm/salesFunnel/index', method: 'get', params }),
    // 添加销售漏斗
    add: (data: any) => request({ url: '/admin/crm/salesFunnel/add', method: 'post', data }),
    // 编辑销售漏斗
    edit: (id: number, data: any) => request({ url: `/admin/crm/salesFunnel/edit/${id}`, method: 'post', data }),
    // 删除销售漏斗
    delete: (id: number) => request({ url: `/admin/crm/salesFunnel/delete/${id}`, method: 'post' }),
    // 获取阶段历史
    getHistory: (funnel_id: number, params: any) => request({ url: `/admin/crm/salesFunnel/history/${funnel_id}`, method: 'get', params }),
}
