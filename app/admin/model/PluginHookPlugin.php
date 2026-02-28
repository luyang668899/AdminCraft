<?php

namespace app\admin\model;

use think\Model;

class PluginHookPlugin extends Model
{
    protected $name = 'plugin_hook_plugin';

    protected $autoWriteTimestamp = false;

    /**
     * 获取钩子关联的插件
     * @param int $hookId 钩子ID
     * @return array
     */
    public static function getByHookId($hookId)
    {
        return self::where('hook_id', $hookId)->where('status', 1)->order('sort desc')->select()->toArray();
    }

    /**
     * 获取插件关联的钩子
     * @param string $pluginName 插件名称
     * @return array
     */
    public static function getByPluginName($pluginName)
    {
        return self::where('plugin_name', $pluginName)->where('status', 1)->order('sort desc')->select()->toArray();
    }

    /**
     * 添加钩子关联
     * @param int $hookId 钩子ID
     * @param string $pluginName 插件名称
     * @param int $sort 排序
     * @return bool
     */
    public static function add($hookId, $pluginName, $sort = 0)
    {
        $hookPlugin = self::where('hook_id', $hookId)->where('plugin_name', $pluginName)->find();
        if ($hookPlugin) {
            // 更新关联
            $hookPlugin->status = 1;
            $hookPlugin->sort = $sort;
            return $hookPlugin->save();
        } else {
            // 新增关联
            $hookPlugin = new self();
            $hookPlugin->hook_id = $hookId;
            $hookPlugin->plugin_name = $pluginName;
            $hookPlugin->status = 1;
            $hookPlugin->sort = $sort;
            return $hookPlugin->save();
        }
    }

    /**
     * 删除钩子关联
     * @param int $hookId 钩子ID
     * @param string $pluginName 插件名称
     * @return bool
     */
    public static function remove($hookId, $pluginName)
    {
        return self::where('hook_id', $hookId)->where('plugin_name', $pluginName)->delete();
    }

    /**
     * 批量添加钩子关联
     * @param array $data 关联数据
     * @return bool
     */
    public static function batchAdd($data)
    {
        foreach ($data as $item) {
            self::add($item['hook_id'], $item['plugin_name'], $item['sort'] ?? 0);
        }
        return true;
    }
}
