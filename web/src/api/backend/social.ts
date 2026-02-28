import createAxios from '/@/utils/axios'

const request = createAxios

// 社交媒体配置相关API
export const socialConfigApi = {
    // 获取社交媒体配置列表
    getList: () =>
        request({
            url: '/admin/social/config/index',
            method: 'get',
        }),

    // 获取单个社交媒体配置
    getOne: (id: number) =>
        request({
            url: `/admin/social/config/read/${id}`,
            method: 'get',
        }),

    // 创建社交媒体配置
    create: (data: any) =>
        request({
            url: '/admin/social/config/save',
            method: 'post',
            data,
        }),

    // 更新社交媒体配置
    update: (id: number, data: any) =>
        request({
            url: `/admin/social/config/update/${id}`,
            method: 'post',
            data,
        }),

    // 删除社交媒体配置
    delete: (id: number) =>
        request({
            url: `/admin/social/config/delete/${id}`,
            method: 'post',
        }),

    // 刷新令牌
    refreshToken: (id: number) =>
        request({
            url: `/admin/social/config/refreshToken/${id}`,
            method: 'post',
        }),

    // 获取授权URL
    getAuthUrl: (id: number, state: string = '') =>
        request({
            url: `/admin/social/config/getAuthUrl/${id}`,
            method: 'get',
            params: { state },
        }),

    // 回调处理
    callback: (params: any) =>
        request({
            url: '/admin/social/config/callback',
            method: 'get',
            params,
        }),
}

// 社交媒体用户相关API
export const socialUserApi = {
    // 获取社交媒体用户列表
    getList: () =>
        request({
            url: '/admin/social/user/index',
            method: 'get',
        }),

    // 获取单个社交媒体用户
    getOne: (id: number) =>
        request({
            url: `/admin/social/user/read/${id}`,
            method: 'get',
        }),

    // 绑定系统用户
    bindUser: (id: number, userId: number) =>
        request({
            url: `/admin/social/user/bindUser/${id}`,
            method: 'post',
            data: { user_id: userId },
        }),

    // 解绑系统用户
    unbindUser: (id: number) =>
        request({
            url: `/admin/social/user/unbindUser/${id}`,
            method: 'post',
        }),

    // 获取用户信息
    getUserInfo: (id: number) =>
        request({
            url: `/admin/social/user/getUserInfo/${id}`,
            method: 'get',
        }),

    // 根据openid获取用户
    getByOpenid: (openid: string, platform: string) =>
        request({
            url: '/admin/social/user/getByOpenid',
            method: 'get',
            params: { openid, platform },
        }),

    // 根据unionid获取用户
    getByUnionid: (unionid: string) =>
        request({
            url: '/admin/social/user/getByUnionid',
            method: 'get',
            params: { unionid },
        }),
}

// 社交媒体分享相关API
export const socialShareApi = {
    // 获取社交媒体分享记录列表
    getList: () =>
        request({
            url: '/admin/social/share/index',
            method: 'get',
        }),

    // 创建社交媒体分享记录
    create: (data: any) =>
        request({
            url: '/admin/social/share/save',
            method: 'post',
            data,
        }),

    // 获取单个社交媒体分享记录
    getOne: (id: number) =>
        request({
            url: `/admin/social/share/read/${id}`,
            method: 'get',
        }),

    // 执行分享
    doShare: (id: number) =>
        request({
            url: `/admin/social/share/doShare/${id}`,
            method: 'post',
        }),

    // 删除社交媒体分享记录
    delete: (id: number) =>
        request({
            url: `/admin/social/share/delete/${id}`,
            method: 'post',
        }),

    // 获取分享统计
    getStats: (params: any) =>
        request({
            url: '/admin/social/share/getStats',
            method: 'get',
            params,
        }),
}

// 社交媒体消息相关API
export const socialMessageApi = {
    // 获取社交媒体消息列表
    getList: () =>
        request({
            url: '/admin/social/message/index',
            method: 'get',
        }),

    // 创建社交媒体消息
    create: (data: any) =>
        request({
            url: '/admin/social/message/save',
            method: 'post',
            data,
        }),

    // 获取单个社交媒体消息
    getOne: (id: number) =>
        request({
            url: `/admin/social/message/read/${id}`,
            method: 'get',
        }),

    // 标记消息为已读
    markAsRead: (id: number) =>
        request({
            url: `/admin/social/message/markAsRead/${id}`,
            method: 'post',
        }),

    // 标记消息为未读
    markAsUnread: (id: number) =>
        request({
            url: `/admin/social/message/markAsUnread/${id}`,
            method: 'post',
        }),

    // 发送消息
    send: (id: number) =>
        request({
            url: `/admin/social/message/send/${id}`,
            method: 'post',
        }),

    // 删除社交媒体消息
    delete: (id: number) =>
        request({
            url: `/admin/social/message/delete/${id}`,
            method: 'post',
        }),

    // 获取未读消息数量
    getUnreadCount: (configId?: number) =>
        request({
            url: '/admin/social/message/getUnreadCount',
            method: 'get',
            params: { config_id: configId },
        }),

    // 获取消息列表
    getMessages: (params: any) =>
        request({
            url: '/admin/social/message/getMessages',
            method: 'get',
            params,
        }),
}

export default {
    socialConfigApi,
    socialUserApi,
    socialShareApi,
    socialMessageApi,
}
