<?php

namespace app\admin\controller\content;

use Throwable;
use think\facade\Db;
use app\admin\model\ContentArticle;
use app\common\controller\Backend;

class Article extends Backend
{
    protected object $model;
    protected string|array $preExcludeFields = ['create_time', 'update_time', 'content'];
    protected string|array $quickSearchField = 'title';
    protected array $withJoinTable = ['category'];
    
    public function initialize(): void
    {
        parent::initialize();
        $this->model = new ContentArticle();
    }
    
    public function index(): void
    {
        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $res = $this->model
            ->field($this->indexField)
            ->withJoin($this->withJoinTable, $this->withJoinType)
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
    
    public function add(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }
            
            $data = $this->excludeFields($data);
            $tags = $data['tags'] ?? [];
            unset($data['tags']);
            
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
                $article = $this->model->save($data);
                if ($article && !empty($tags)) {
                    $this->model->tags()->attach($tags);
                }
                $this->model->commit();
                $result = true;
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result) {
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
            $tags = $data['tags'] ?? [];
            unset($data['tags']);
            
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
                $article = $row->save($data);
                if ($article) {
                    $row->tags()->detach();
                    if (!empty($tags)) {
                        $row->tags()->attach($tags);
                    }
                }
                $this->model->commit();
                $result = true;
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result) {
                $this->success(__('Update successful'));
            } else {
                $this->error(__('No rows updated'));
            }
        }
        
        // 获取文章关联的标签
        $row->tags = $row->tags()->column('id');
        
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
                // 先删除标签关联
                $v->tags()->detach();
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
    
    public function publish(): void
    {
        $ids = $this->request->param('ids/a', []);
        if (empty($ids)) {
            $this->error(__('Parameter error'));
        }
        
        $result = $this->model->where('id', 'in', $ids)->update(['status' => 'published']);
        if ($result) {
            $this->success(__('Published successfully'));
        } else {
            $this->error(__('Operation failed'));
        }
    }
    
    public function draft(): void
    {
        $ids = $this->request->param('ids/a', []);
        if (empty($ids)) {
            $this->error(__('Parameter error'));
        }
        
        $result = $this->model->where('id', 'in', $ids)->update(['status' => 'draft']);
        if ($result) {
            $this->success(__('Saved as draft successfully'));
        } else {
            $this->error(__('Operation failed'));
        }
    }
}