import createAxios from '/@/utils/axios'

const request = createAxios

// 商品分类相关API
export const categoryApi = {
    // 获取分类树
    getTree: () => request({ url: '/admin/ecommerce/category/index', method: 'get' }),
    // 添加分类
    add: (data: any) => request({ url: '/admin/ecommerce/category/add', method: 'post', data }),
    // 编辑分类
    edit: (id: number, data: any) => request({ url: `/admin/ecommerce/category/edit/${id}`, method: 'post', data }),
    // 删除分类
    delete: (id: number) => request({ url: `/admin/ecommerce/category/delete/${id}`, method: 'post' }),
}

// 商品相关API
export const goodsApi = {
    // 获取商品列表
    getList: (params: any) => request({ url: '/admin/ecommerce/goods/index', method: 'get', params }),
    // 添加商品
    add: (data: any) => request({ url: '/admin/ecommerce/goods/add', method: 'post', data }),
    // 编辑商品
    edit: (id: number, data: any) => request({ url: `/admin/ecommerce/goods/edit/${id}`, method: 'post', data }),
    // 删除商品
    delete: (id: number) => request({ url: `/admin/ecommerce/goods/delete/${id}`, method: 'post' }),
    // 切换商品状态
    toggleStatus: (id: number) => request({ url: `/admin/ecommerce/goods/toggleStatus/${id}`, method: 'post' }),
}

// 订单相关API
export const orderApi = {
    // 获取订单列表
    getList: (params: any) => request({ url: '/admin/ecommerce/order/index', method: 'get', params }),
    // 查看订单详情
    view: (id: number) => request({ url: `/admin/ecommerce/order/view/${id}`, method: 'get' }),
    // 更新订单状态
    updateStatus: (id: number, data: any) => request({ url: `/admin/ecommerce/order/updateStatus/${id}`, method: 'post', data }),
    // 删除订单
    delete: (id: number) => request({ url: `/admin/ecommerce/order/delete/${id}`, method: 'post' }),
}
