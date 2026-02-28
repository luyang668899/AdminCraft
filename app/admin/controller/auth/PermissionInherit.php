<?php

namespace app\admin\controller\auth;

use Throwable;
use app\common\controller\Backend;
use app\admin\model\AdminPermissionInherit;
use app\admin\model\AdminGroup;

class PermissionInherit extends Backend
{
    protected string|array $preExcludeFields = ['create_time', 'update_time'];

    protected string|array $defaultSortField = ['id' => 'desc'];

    protected string|array $quickSearchField = '';

    /**
     * @var object
     * @phpstan-var AdminPermissionInherit
     */
    protected object $model;

    public function initialize(): void
    {
        parent::initialize();
        $this->model = new AdminPermissionInherit();
    }

    public function index(): void
    {
        if ($this->request->param('select')) {
            $this->select();
        }

        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $res = $this->model
            ->with(['parentGroup', 'childGroup'])
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
     * 添加
     */
    public function add(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }

            // 检查是否存在循环继承
            if ($this->checkCircularInheritance($data['parent_group_id'], $data['child_group_id'])) {
                $this->error(__('Circular inheritance detected'));
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
     * 编辑
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

            // 检查是否存在循环继承
            if ($this->checkCircularInheritance($data['parent_group_id'], $data['child_group_id'], $id)) {
                $this->error(__('Circular inheritance detected'));
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
     * 删除
     * @throws Throwable
     */
    public function del(): void
    {
        $ids = $this->request->param('ids/a', []);
        parent::del();
    }

    /**
     * 重写select方法
     * @throws Throwable
     */
    public function select(): void
    {
        $data = $this->model->where('status', 1)->select()->toArray();
        $this->success('', [
            'options' => $data
        ]);
    }

    /**
     * 检查循环继承
     * @param int $parentId 父角色ID
     * @param int $childId 子角色ID
     * @param int $excludeId 排除的ID（用于编辑时）
     * @return bool
     */
    private function checkCircularInheritance(int $parentId, int $childId, int $excludeId = 0): bool
    {
        // 不能自继承
        if ($parentId == $childId) {
            return true;
        }

        // 检查是否存在子角色是父角色的父角色
        $inheritances = AdminPermissionInherit::where('status', 1)->where('id', '<>', $excludeId)->select();
        $inheritMap = [];
        
        foreach ($inheritances as $inherit) {
            $inheritMap[$inherit['child_group_id']][] = $inherit['parent_group_id'];
        }

        // 递归检查
        return $this->hasCircularPath($childId, $parentId, $inheritMap, []);
    }

    /**
     * 递归检查是否存在循环路径
     * @param int $current 当前角色ID
     * @param int $target 目标角色ID
     * @param array $inheritMap 继承关系映射
     * @param array $visited 已访问的角色ID
     * @return bool
     */
    private function hasCircularPath(int $current, int $target, array $inheritMap, array $visited): bool
    {
        if (in_array($current, $visited)) {
            return false;
        }

        $visited[] = $current;

        if ($current == $target) {
            return true;
        }

        if (!isset($inheritMap[$current])) {
            return false;
        }

        foreach ($inheritMap[$current] as $parentId) {
            if ($this->hasCircularPath($parentId, $target, $inheritMap, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取角色列表
     */
    public function getGroups(): void
    {
        $groups = AdminGroup::where('status', 1)->select()->toArray();
        $this->success('', [
            'groups' => $groups
        ]);
    }
}
