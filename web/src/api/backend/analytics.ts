import createAxios from '/@/utils/axios'

const request = createAxios

// 仪表盘管理API
export const dashboardApi = {
    // 获取仪表盘列表
    getList: (params: any) => {
        return request({
            url: '/admin/analytics/dashboard/list',
            method: 'get',
            params,
        })
    },

    // 创建仪表盘
    create: (data: any) => {
        return request({
            url: '/admin/analytics/dashboard/save',
            method: 'post',
            data,
        })
    },

    // 更新仪表盘
    update: (data: any) => {
        return request({
            url: '/admin/analytics/dashboard/update',
            method: 'post',
            data,
        })
    },

    // 删除仪表盘
    delete: (id: number) => {
        return request({
            url: `/admin/analytics/dashboard/delete/${id}`,
            method: 'get',
        })
    },

    // 获取仪表盘的报表列表
    getReports: (dashboardId: number) => {
        return request({
            url: `/admin/analytics/dashboard/reports/${dashboardId}`,
            method: 'get',
        })
    },
}

// 报表管理API
export const reportApi = {
    // 获取报表列表
    getList: (params: any) => {
        return request({
            url: '/admin/analytics/report/list',
            method: 'get',
            params,
        })
    },

    // 创建报表
    create: (data: any) => {
        return request({
            url: '/admin/analytics/report/save',
            method: 'post',
            data,
        })
    },

    // 更新报表
    update: (data: any) => {
        return request({
            url: '/admin/analytics/report/update',
            method: 'post',
            data,
        })
    },

    // 删除报表
    delete: (id: number) => {
        return request({
            url: `/admin/analytics/report/delete/${id}`,
            method: 'get',
        })
    },

    // 获取报表的图表列表
    getCharts: (reportId: number) => {
        return request({
            url: `/admin/analytics/report/charts/${reportId}`,
            method: 'get',
        })
    },
}

// 图表管理API
export const chartApi = {
    // 获取图表列表
    getList: (params: any) => {
        return request({
            url: '/admin/analytics/chart/list',
            method: 'get',
            params,
        })
    },

    // 创建图表
    create: (data: any) => {
        return request({
            url: '/admin/analytics/chart/save',
            method: 'post',
            data,
        })
    },

    // 更新图表
    update: (data: any) => {
        return request({
            url: '/admin/analytics/chart/update',
            method: 'post',
            data,
        })
    },

    // 删除图表
    delete: (id: number) => {
        return request({
            url: `/admin/analytics/chart/delete/${id}`,
            method: 'get',
        })
    },
}

// 可视化管理API
export const visualizationApi = {
    // 获取可视化列表
    getList: (params: any) => {
        return request({
            url: '/admin/analytics/visualization/list',
            method: 'get',
            params,
        })
    },

    // 创建可视化
    create: (data: any) => {
        return request({
            url: '/admin/analytics/visualization/save',
            method: 'post',
            data,
        })
    },

    // 更新可视化
    update: (data: any) => {
        return request({
            url: '/admin/analytics/visualization/update',
            method: 'post',
            data,
        })
    },

    // 删除可视化
    delete: (id: number) => {
        return request({
            url: `/admin/analytics/visualization/delete/${id}`,
            method: 'get',
        })
    },
}
