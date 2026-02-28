<?php

namespace app\admin\controller\analytics;

use app\admin\model\AnalyticsDashboard;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Dashboard extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    
    public function list()
    {
        $where = [];
        $name = Request::param('name');
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $dashboards = AnalyticsDashboard::where($where)
            ->with(['creator'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $dashboards->total(),
            'data' => $dashboards->items()
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
            AnalyticsDashboard::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'config' => $data['config'],
                'creator_id' => $this->adminId
            ]);
            
            return json(['code' => 0, 'msg' => '创建成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $dashboard = AnalyticsDashboard::find($id);
        View::assign('dashboard', $dashboard);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $dashboard = AnalyticsDashboard::find($data['dashboard_id']);
            $dashboard->save([
                'name' => $data['name'],
                'description' => $data['description'],
                'config' => $data['config']
            ]);
            
            return json(['code' => 0, 'msg' => '更新成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function delete($id)
    {
        try {
            AnalyticsDashboard::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function reports($dashboard_id)
    {
        $dashboard = AnalyticsDashboard::find($dashboard_id);
        $reports = $dashboard->reports()->with(['creator'])->select();
        
        return json(['code' => 0, 'msg' => '', 'data' => $reports]);
    }
}