<?php

namespace app\admin\controller\plugin;

use Throwable;
use app\common\controller\Backend;
use app\admin\model\PluginHook;
use app\admin\model\PluginHookPlugin;

class Hook extends Backend
{
    protected string|array $preExcludeFields = [];

    protected string|array $defaultSortField = ['sort' => 'desc', 'id' => 'desc'];

    protected string|array $quickSearchField = 'title';

    /**
     * @var object
     * @phpstan-var PluginHook
     */
    protected object $model;

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new PluginHook();
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
     * 添加钩子
     */
    public function add(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }

            $data = $this->excludeFields($data);
            if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                $data[$this->dataLimitField] = $this->auth->id;
            }

            $result = false;
            $this->model->startTrans();
            try {
                // 模型验证
                if ($this->modelValidate) {
                    $validate = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    if (class_exists($validate)) {
                        $validate = new $validate();
                        if ($this->modelSceneValidate) $validate->scene('add');
                        $validate->check($data);
                    }
                }
                $result = $this->model->save($data);
                $this->model->commit();
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success(__('Added successfully'));
            } else {
                $this->error(__('No rows were added'));
            }
        }

        $this->error(__('Parameter error'));
    }

    /**
     * 编辑钩子
     * @throws Throwable
     */
    public function edit(): void
    {
        $id  = $this->request->param($this->model->getPk());
        $row = $this->model->find($id);
        if (!$row) {
            $this->error(__('Record not found'));
        }

        $dataLimitAdminIds = $this->getDataLimitAdminIds();
        if ($dataLimitAdminIds && !in_array($row[$this->dataLimitField], $dataLimitAdminIds)) {
            $this->error(__('You have no permission'));
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }

            $data   = $this->excludeFields($data);
            $result = false;
            $this->model->startTrans();
            try {
                // 模型验证
                if ($this->modelValidate) {
                    $validate = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    if (class_exists($validate)) {
                        $validate = new $validate();
                        if ($this->modelSceneValidate) $validate->scene('edit');
                        $validate->check($data);
                    }
                }
                $result = $row->save($data);
                $this->model->commit();
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success(__('Update successful'));
            } else {
                $this->error(__('No rows updated'));
            }
        }

        $this->success('', [
            'row' => $row
        ]);
    }

    /**
     * 删除钩子
     * @throws Throwable
     */
    public function del(): void
    {
        $ids = $this->request->param('ids/a', []);
        
        // 删除钩子关联
        foreach ($ids as $id) {
            PluginHookPlugin::where('hook_id', $id)->delete();
        }
        
        parent::del();
    }

    /**
     * 获取钩子关联的插件
     */
    public function getHookPlugins(): void
    {
        $hookId = $this->request->param('hook_id');
        if (!$hookId) {
            $this->error(__('Parameter %s can not be empty', ['hook_id']));
        }

        $plugins = PluginHookPlugin::getByHookId($hookId);
        $this->success('', [
            'plugins' => $plugins
        ]);
    }

    /**
     * 关联插件到钩子
     */
    public function addHookPlugin(): void
    {
        $hookId = $this->request->param('hook_id');
        $pluginName = $this->request->param('plugin_name');
        $sort = $this->request->param('sort', 0);
        
        if (!$hookId || !$pluginName) {
            $this->error(__('Parameter error'));
        }

        $result = PluginHookPlugin::add($hookId, $pluginName, $sort);
        if ($result) {
            $this->success(__('Added successfully'));
        } else {
            $this->error(__('Add failed'));
        }
    }

    /**
     * 从钩子中移除插件
     */
    public function removeHookPlugin(): void
    {
        $hookId = $this->request->param('hook_id');
        $pluginName = $this->request->param('plugin_name');
        
        if (!$hookId || !$pluginName) {
            $this->error(__('Parameter error'));
        }

        $result = PluginHookPlugin::remove($hookId, $pluginName);
        if ($result) {
            $this->success(__('Removed successfully'));
        } else {
            $this->error(__('Remove failed'));
        }
    }

    /**
     * 执行钩子
     */
    public function execute(): void
    {
        $name = $this->request->param('name');
        $params = $this->request->param('params', []);
        
        if (!$name) {
            $this->error(__('Parameter %s can not be empty', ['name']));
        }

        $result = PluginHook::execute($name, $params);
        $this->success('', [
            'result' => $result
        ]);
    }
}
