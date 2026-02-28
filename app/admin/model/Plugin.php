<?php

namespace app\admin\model;

use think\Model;
use think\facade\Filesystem;
use think\facade\App;

class Plugin extends Model
{
    protected $name = 'plugin';

    protected $autoWriteTimestamp = false;

    /**
     * 获取插件列表
     * @param array $where 条件
     * @return array
     */
    public static function getList($where = [])
    {
        return self::where($where)->order('sort desc, id desc')->select()->toArray();
    }

    /**
     * 获取已启用的插件
     * @return array
     */
    public static function getEnabledPlugins()
    {
        return self::where('status', 2)->order('sort desc')->select()->toArray();
    }

    /**
     * 安装插件
     * @param string $name 插件名称
     * @return bool
     */
    public static function install($name)
    {
        $pluginPath = App::getRootPath() . 'plugin/' . $name;
        if (!is_dir($pluginPath)) {
            return false;
        }

        // 读取插件信息
        $infoFile = $pluginPath . '/info.php';
        if (!file_exists($infoFile)) {
            return false;
        }

        $info = include $infoFile;
        if (!isset($info['name']) || !isset($info['title'])) {
            return false;
        }

        // 检查是否已存在
        $plugin = self::where('name', $name)->find();
        if ($plugin) {
            // 更新插件信息
            $plugin->title = $info['title'];
            $plugin->description = $info['description'] ?? '';
            $plugin->version = $info['version'] ?? '1.0.0';
            $plugin->author = $info['author'] ?? '';
            $plugin->website = $info['website'] ?? '';
            $plugin->status = 1;
            $plugin->update_time = date('Y-m-d H:i:s');
            return $plugin->save();
        } else {
            // 新增插件
            $plugin = new self();
            $plugin->name = $name;
            $plugin->title = $info['title'];
            $plugin->description = $info['description'] ?? '';
            $plugin->version = $info['version'] ?? '1.0.0';
            $plugin->author = $info['author'] ?? '';
            $plugin->website = $info['website'] ?? '';
            $plugin->status = 1;
            $plugin->install_time = date('Y-m-d H:i:s');
            $plugin->update_time = date('Y-m-d H:i:s');
            return $plugin->save();
        }
    }

    /**
     * 卸载插件
     * @param string $name 插件名称
     * @return bool
     */
    public static function uninstall($name)
    {
        $plugin = self::where('name', $name)->find();
        if (!$plugin) {
            return false;
        }

        // 执行插件卸载方法
        $pluginPath = App::getRootPath() . 'plugin/' . $name;
        $uninstallFile = $pluginPath . '/uninstall.php';
        if (file_exists($uninstallFile)) {
            include $uninstallFile;
            if (function_exists('uninstall')) {
                uninstall();
            }
        }

        // 删除插件钩子关联
        PluginHookPlugin::where('plugin_name', $name)->delete();

        // 删除插件记录
        return $plugin->delete();
    }

    /**
     * 启用插件
     * @param string $name 插件名称
     * @return bool
     */
    public static function enable($name)
    {
        $plugin = self::where('name', $name)->find();
        if (!$plugin) {
            return false;
        }

        // 执行插件启用方法
        $pluginPath = App::getRootPath() . 'plugin/' . $name;
        $enableFile = $pluginPath . '/enable.php';
        if (file_exists($enableFile)) {
            include $enableFile;
            if (function_exists('enable')) {
                enable();
            }
        }

        $plugin->status = 2;
        return $plugin->save();
    }

    /**
     * 禁用插件
     * @param string $name 插件名称
     * @return bool
     */
    public static function disable($name)
    {
        $plugin = self::where('name', $name)->find();
        if (!$plugin) {
            return false;
        }

        // 执行插件禁用方法
        $pluginPath = App::getRootPath() . 'plugin/' . $name;
        $disableFile = $pluginPath . '/disable.php';
        if (file_exists($disableFile)) {
            include $disableFile;
            if (function_exists('disable')) {
                disable();
            }
        }

        $plugin->status = 1;
        return $plugin->save();
    }

    /**
     * 获取插件配置
     * @param string $name 插件名称
     * @return array
     */
    public static function getConfig($name)
    {
        $plugin = self::where('name', $name)->find();
        if (!$plugin || !$plugin->config) {
            return [];
        }
        return json_decode($plugin->config, true);
    }

    /**
     * 设置插件配置
     * @param string $name 插件名称
     * @param array $config 配置
     * @return bool
     */
    public static function setConfig($name, $config)
    {
        $plugin = self::where('name', $name)->find();
        if (!$plugin) {
            return false;
        }
        $plugin->config = json_encode($config);
        return $plugin->save();
    }
}
