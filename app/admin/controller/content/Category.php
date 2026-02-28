<?php

namespace app\admin\controller\content;

use ba\Tree;
use Throwable;
use app\admin\model\ContentCategory;
use app\common\controller\Backend;

class Category extends Backend
{
    protected object $model;
    protected string|array $preExcludeFields = ['create_time', 'update_time'];
    protected string|array $quickSearchField = 'name';
    protected Tree $tree;
    
    public function initialize(): void
    {
        parent::initialize();
        $this->model = new ContentCategory();
        $this->tree = Tree::instance();
    }
    
    public function index(): void
    {
        if ($this->request->param('select')) {
            $this->select();
        }
        
        $list = $this->model->order('weigh desc, id desc')->select()->toArray();
        $list = $this->tree->assembleChild($list);
        
        $this->success('', [
            'list'   => $list,
            'remark' => get_route_remark(),
        ]);
    }
    
    public function select(): void
    {
        $list = $this->model->where('status', 1)->order('weigh desc, id desc')->select()->toArray();
        $list = $this->tree->assembleTree($this->tree->getTreeArray($list));
        
        $this->success('', [
            'options' => $list
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
        
        // 检查是否有子分类
        foreach ($data as $item) {
            $children = $this->model->where('pid', $item->id)->count();
            if ($children > 0) {
                $this->error(__('Please delete child categories first'));
            }
        }
        
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