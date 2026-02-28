import createAxios from '/@/utils/axios'

const request = createAxios

// 插件管理
export const pluginApi = {
    // 获取插件列表
    getList: (params: any) => {
        return request({
            url: '/admin/plugin/plugin/index',
            method: 'get',
            params,
        })
    },
    // 获取所有插件（包括未安装的）
    getPluginList: () => {
        return request({
            url: '/admin/plugin/plugin/getPluginList',
            method: 'get',
        })
    },
    // 安装插件
    install: (name: string) => {
        return request({
            url: '/admin/plugin/plugin/install',
            method: 'post',
            params: { name },
        })
    },
    // 卸载插件
    uninstall: (name: string) => {
        return request({
            url: '/admin/plugin/plugin/uninstall',
            method: 'post',
            params: { name },
        })
    },
    // 启用插件
    enable: (name: string) => {
        return request({
            url: '/admin/plugin/plugin/enable',
            method: 'post',
            params: { name },
        })
    },
    // 禁用插件
    disable: (name: string) => {
        return request({
            url: '/admin/plugin/plugin/disable',
            method: 'post',
            params: { name },
        })
    },
    // 获取插件配置
    getConfig: (name: string) => {
        return request({
            url: '/admin/plugin/plugin/getConfig',
            method: 'get',
            params: { name },
        })
    },
    // 设置插件配置
    setConfig: (name: string, config: any) => {
        return request({
            url: '/admin/plugin/plugin/setConfig',
            method: 'post',
            params: { name },
            data: { config },
        })
    },
}

// 钩子管理
export const hookApi = {
    // 获取钩子列表
    getList: (params: any) => {
        return request({
            url: '/admin/plugin/hook/index',
            method: 'get',
            params,
        })
    },
    // 添加钩子
    add: (data: any) => {
        return request({
            url: '/admin/plugin/hook/add',
            method: 'post',
            data,
        })
    },
    // 编辑钩子
    edit: (data: any) => {
        return request({
            url: '/admin/plugin/hook/edit',
            method: 'post',
            data,
        })
    },
    // 删除钩子
    del: (ids: number[]) => {
        return request({
            url: '/admin/plugin/hook/del',
            method: 'post',
            data: { ids },
        })
    },
    // 获取钩子关联的插件
    getHookPlugins: (hookId: number) => {
        return request({
            url: '/admin/plugin/hook/getHookPlugins',
            method: 'get',
            params: { hook_id: hookId },
        })
    },
    // 关联插件到钩子
    addHookPlugin: (hookId: number, pluginName: string, sort: number = 0) => {
        return request({
            url: '/admin/plugin/hook/addHookPlugin',
            method: 'post',
            params: { hook_id: hookId, plugin_name: pluginName, sort },
        })
    },
    // 从钩子中移除插件
    removeHookPlugin: (hookId: number, pluginName: string) => {
        return request({
            url: '/admin/plugin/hook/removeHookPlugin',
            method: 'post',
            params: { hook_id: hookId, plugin_name: pluginName },
        })
    },
    // 执行钩子
    execute: (name: string, params: any = {}) => {
        return request({
            url: '/admin/plugin/hook/execute',
            method: 'get',
            params: { name, params },
        })
    },
}
