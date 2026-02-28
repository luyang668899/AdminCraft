<?php

namespace app\admin\model;

use think\Model;

class PluginHook extends Model
{
    protected $name = 'plugin_hook';

    protected $autoWriteTimestamp = false;

    /**
     * 获取钩子列表
     * @param array $where 条件
     * @return array
     */
    public static function getList($where = [])
    {
        return self::where($where)->order('sort desc, id desc')->select()->toArray();
    }

    /**
     * 获取钩子详情
     * @param string $name 钩子名称
     * @return array
     */
    public static function getByName($name)
    {
        return self::where('name', $name)->find();
    }

    /**
     * 执行钩子
     * @param string $name 钩子名称
     * @param array $params 参数
     * @return mixed
     */
    public static function execute($name, $params = [])
    {
        $hook = self::getByName($name);
        if (!$hook || $hook->status != 1) {
            return null;
        }

        // 获取钩子关联的插件
        $hookPlugins = PluginHookPlugin::where('hook_id', $hook->id)->where('status', 1)->order('sort desc')->select();
        if (empty($hookPlugins)) {
            return null;
        }

        $results = [];
        foreach ($hookPlugins as $hookPlugin) {
            $pluginName = $hookPlugin->plugin_name;
            $plugin = Plugin::where('name', $pluginName)->where('status', 2)->find();
            if (!$plugin) {
                continue;
            }

            // 执行插件的钩子方法
            $hookFile = app()->getRootPath() . 'plugin/' . $pluginName . '/hook.php';
            if (file_exists($hookFile)) {
                include $hookFile;
                $hookFunction = 'hook_' . $name;
                if (function_exists($hookFunction)) {
                    $results[] = $hookFunction($params);
                }
            }
        }

        return $results;
    }

    /**
     * 注册钩子
     * @param string $name 钩子名称
     * @param string $title 钩子标题
     * @param string $description 钩子描述
     * @param string $type 钩子类型
     * @return bool
     */
    public static function register($name, $title, $description = '', $type = 'app')
    {
        $hook = self::getByName($name);
        if ($hook) {
            // 更新钩子信息
            $hook->title = $title;
            $hook->description = $description;
            $hook->type = $type;
            return $hook->save();
        } else {
            // 新增钩子
            $hook = new self();
            $hook->name = $name;
            $hook->title = $title;
            $hook->description = $description;
            $hook->type = $type;
            $hook->status = 1;
            return $hook->save();
        }
    }
}
