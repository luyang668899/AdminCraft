<?php

namespace app\admin\controller\project;

use app\admin\model\ProjectTask;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Task extends BaseController
{
    public function index($project_id)
    {
        View::assign('project_id', $project_id);
        return View::fetch();
    }
    
    public function list()
    {
        $project_id = Request::param('project_id');
        $where = ['project_id' => $project_id];
        
        $title = Request::param('title');
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        
        $status = Request::param('status');
        if ($status !== null) {
            $where[] = ['status', '=', $status];
        }
        
        $priority = Request::param('priority');
        if ($priority !== null) {
            $where[] = ['priority', '=', $priority];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $tasks = ProjectTask::where($where)
            ->with(['assignee', 'creator'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $tasks->total(),
            'data' => $tasks->items()
        ]);
    }
    
    public function add($project_id)
    {
        View::assign('project_id', $project_id);
        return View::fetch();
    }
    
    public function save()
    {
        $data = Request::post();
        
        try {
            ProjectTask::create([
                'project_id' => $data['project_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'assignee_id' => $data['assignee_id'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'creator_id' => $this->adminId
            ]);
            
            return json(['code' => 0, 'msg' => '创建成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $task = ProjectTask::find($id);
        View::assign('task', $task);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $task = ProjectTask::find($data['task_id']);
            $task->save([
                'title' => $data['title'],
                'description' => $data['description'],
                'assignee_id' => $data['assignee_id'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);
            
            return json(['code' => 0, 'msg' => '更新成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function delete($id)
    {
        try {
            ProjectTask::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function updateStatus()
    {
        $data = Request::post();
        
        try {
            $task = ProjectTask::find($data['task_id']);
            $task->save(['status' => $data['status']]);
            
            return json(['code' => 0, 'msg' => '更新状态成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}