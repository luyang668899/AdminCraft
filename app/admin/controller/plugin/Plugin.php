<?php

namespace app\admin\controller\plugin;

use Throwable;
use think\facade\App;
use app\common\controller\Backend;
use app\admin\model\Plugin;
use app\admin\model\PluginHook;
use app\admin\model\PluginHookPlugin;

class Plugin extends Backend
{
    protected string|array $preExcludeFields = ['install_time', 'update_time'];

    protected string|array $defaultSortField = ['sort' => 'desc', 'id' => 'desc'];

    protected string|array $quickSearchField = 'title';

    /**
     * @var object
     * @phpstan-var Plugin
     */
    protected object $model;

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new Plugin();
    }

    public function index(): void
    {
        if ($this->request->param('select')) {
            $this->select();
        }

        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $res = $this->model
            ->alias($alias)
            ->where($where)
            ->order($order)
            ->paginate($limit);

        $this->success('', [
            'list'   => $res->items(),
            'total'  => $res->total(),
            'remark' => get_route_remark(),
        ]);
    }

    /**
     * 安装插件
     */
    public function install(): void
    {
        $name = $this->request->param('name');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = Plugin::install($name);
        if ($result) {
            $this->success(__('Installed successfully'));
        } else {
            $this->error(__('Installation failed'));
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(): void
    {
        $name = $this->request->param('name');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = Plugin::uninstall($name);
        if ($result) {
            $this->success(__('Uninstalled successfully'));
        } else {
            $this->error(__('Uninstallation failed'));
        }
    }

    /**
     * 启用插件
     */
    public function enable(): void
    {
        $name = $this->request->param('name');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = Plugin::enable($name);
        if ($result) {
            $this->success(__('Enabled successfully'));
        } else {
            $this->error(__('Enable failed'));
        }
    }

    /**
     * 禁用插件
     */
    public function disable(): void
    {
        $name = $this->request->param('name');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = Plugin::disable($name);
        if ($result) {
            $this->success(__('Disabled successfully'));
        } else {
            $this->error(__('Disable failed'));
        }
    }

    /**
     * 获取插件配置
     */
    public function getConfig(): void
    {
        $name = $this->request->param('name');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $config = Plugin::getConfig($name);
        $this->success('', [
            'config' => $config
        ]);
    }

    /**
     * 设置插件配置
     */
    public function setConfig(): void
    {
        $name = $this->request->param('name');
        $config = $this->request->post('config');
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = Plugin::setConfig($name, $config);
        if ($result) {
            $this->success(__('Configuration saved successfully'));
        } else {
            $this->error(__('Configuration save failed'));
        }
    }

    /**
     * 获取插件列表（包括未安装的）
     */
    public function getPluginList(): void
    {
        $pluginPath = App::getRootPath() . 'plugin';
        $plugins = [];

        if (is_dir($pluginPath)) {
            $dirs = scandir($pluginPath);
            foreach ($dirs as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }

                $pluginDir = $pluginPath . '/' . $dir;
                if (is_dir($pluginDir)) {
                    $infoFile = $pluginDir . '/info.php';
                    if (file_exists($infoFile)) {
                        $info = include $infoFile;
                        if (isset($info['name']) && isset($info['title'])) {
                            // 检查插件状态
                            $plugin = Plugin::where('name', $info['name'])->find();
                            $info['status'] = $plugin ? $plugin->status : 0;
                            $plugins[] = $info;
                        }
                    }
                }
            }
        }

        $this->success('', [
            'plugins' => $plugins
        ]);
    }
}
