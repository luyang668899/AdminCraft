import createAxios from '/@/utils/axios'
const request = createAxios

// 分类管理
export const categoryApi = {
    // 获取分类列表
    getList: (params: any) => request({ url: '/admin/content.category/index', method: 'get', params }),
    // 获取分类详情
    getInfo: (params: any) => request({ url: '/admin/content.category/read', method: 'get', params }),
    // 添加分类
    add: (data: any) => request({ url: '/admin/content.category/add', method: 'post', data }),
    // 编辑分类
    edit: (data: any) => request({ url: '/admin/content.category/edit', method: 'post', data }),
    // 删除分类
    delete: (data: any) => request({ url: '/admin/content.category/delete', method: 'post', data }),
    // 获取分类树
    getTree: (params: any) => request({ url: '/admin/content.category/tree', method: 'get', params }),
}

// 标签管理
export const tagApi = {
    // 获取标签列表
    getList: (params: any) => request({ url: '/admin/content.tag/index', method: 'get', params }),
    // 获取标签详情
    getInfo: (params: any) => request({ url: '/admin/content.tag/read', method: 'get', params }),
    // 添加标签
    add: (data: any) => request({ url: '/admin/content.tag/add', method: 'post', data }),
    // 编辑标签
    edit: (data: any) => request({ url: '/admin/content.tag/edit', method: 'post', data }),
    // 删除标签
    delete: (data: any) => request({ url: '/admin/content.tag/delete', method: 'post', data }),
}

// 文章管理
export const articleApi = {
    // 获取文章列表
    getList: (params: any) => request({ url: '/admin/content.article/index', method: 'get', params }),
    // 获取文章详情
    getInfo: (params: any) => request({ url: '/admin/content.article/read', method: 'get', params }),
    // 添加文章
    add: (data: any) => request({ url: '/admin/content.article/add', method: 'post', data }),
    // 编辑文章
    edit: (data: any) => request({ url: '/admin/content.article/edit', method: 'post', data }),
    // 删除文章
    delete: (data: any) => request({ url: '/admin/content.article/delete', method: 'post', data }),
    // 发布文章
    publish: (data: any) => request({ url: '/admin/content.article/publish', method: 'post', data }),
    // 草稿文章
    draft: (data: any) => request({ url: '/admin/content.article/draft', method: 'post', data }),
}
