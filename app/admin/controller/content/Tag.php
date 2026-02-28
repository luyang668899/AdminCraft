<?php

namespace app\admin\controller\content;

use Throwable;
use app\admin\model\ContentTag;
use app\common\controller\Backend;

class Tag extends Backend
{
    protected object $model;
    protected string|array $preExcludeFields = ['create_time', 'update_time'];
    protected string|array $quickSearchField = 'name';
    
    public function initialize(): void
    {
        parent::initialize();
        $this->model = new ContentTag();
    }
    
    public function index(): void
    {
        if ($this->request->param('select')) {
            $this->select();
        }
        
        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $res = $this->model
            ->field($this->indexField)
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
    
    public function select(): void
    {
        $data = $this->model->where('status', 1)->order('id desc')->select()->toArray();
        $options = [];
        foreach ($data as $item) {
            $options[] = [
                'label' => $item['name'],
                'value' => $item['id'],
            ];
        }
        
        $this->success('', [
            'options' => $options
        ]);
    }
    
    public function add(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }
            
            $data = $this->excludeFields($data);
            
            $result = false;
            $this->model->startTrans();
            try {
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
    
    public function edit(): void
    {
        $pk = $this->model->getPk();
        $id = $this->request->param($pk);
        $row = $this->model->find($id);
        if (!$row) {
            $this->error(__('Record not found'));
        }
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }
            
            $data = $this->excludeFields($data);
            
            $result = false;
            $this->model->startTrans();
            try {
                if ($this->modelValidate) {
                    $validate = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    if (class_exists($validate)) {
                        $validate = new $validate();
                        if ($this->modelSceneValidate) $validate->scene('edit');
                        $data[$pk] = $row[$pk];
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
    
    public function del(): void
    {
        $ids = $this->request->param('ids/a', []);
        $data = $this->model->where($this->model->getPk(), 'in', $ids)->select();
        
        $count = 0;
        $this->model->startTrans();
        try {
            foreach ($data as $v) {
                $count += $v->delete();
            }
            $this->model->commit();
        } catch (Throwable $e) {
            $this->model->rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success(__('Deleted successfully'));
        } else {
            $this->error(__('No rows were deleted'));
        }
    }
}