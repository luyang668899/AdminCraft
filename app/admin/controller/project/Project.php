<?php

namespace app\admin\controller\project;

use app\admin\model\Project;
use app\admin\model\ProjectMember;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Project extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    
    public function list()
    {
        $where = [];
        $title = Request::param('title');
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        
        $status = Request::param('status');
        if ($status !== null) {
            $where[] = ['status', '=', $status];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $projects = Project::where($where)
            ->with(['creator'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $projects->total(),
            'data' => $projects->items()
        ]);
    }
    
    public function add()
    {
        return View::fetch();
    }
    
    public function save()
    {
        $data = Request::post();
        
        try {
            $project = Project::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
                'creator_id' => $this->adminId
            ]);
            
            // 添加创建者为项目成员
            ProjectMember::create([
                'project_id' => $project->project_id,
                'user_id' => $this->adminId,
                'role' => 2 // 创建者
            ]);
            
            return json(['code' => 0, 'msg' => '创建成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $project = Project::find($id);
        View::assign('project', $project);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $project = Project::find($data['project_id']);
            $project->save([
                'title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status']
            ]);
            
            return json(['code' => 0, 'msg' => '更新成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function delete($id)
    {
        try {
            Project::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function members($project_id)
    {
        $members = ProjectMember::where('project_id', $project_id)
            ->with(['user'])
            ->select();
        
        return json(['code' => 0, 'msg' => '', 'data' => $members]);
    }
    
    public function addMember()
    {
        $data = Request::post();
        
        try {
            ProjectMember::create([
                'project_id' => $data['project_id'],
                'user_id' => $data['user_id'],
                'role' => $data['role']
            ]);
            
            return json(['code' => 0, 'msg' => '添加成员成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function removeMember($id)
    {
        try {
            ProjectMember::destroy($id);
            return json(['code' => 0, 'msg' => '移除成员成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}