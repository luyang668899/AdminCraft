<?php

namespace app\admin\controller\analytics;

use app\admin\model\AnalyticsReport;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Report extends BaseController
{
    public function index($dashboard_id)
    {
        View::assign('dashboard_id', $dashboard_id);
        return View::fetch();
    }
    
    public function list()
    {
        $dashboard_id = Request::param('dashboard_id');
        $where = ['dashboard_id' => $dashboard_id];
        
        $name = Request::param('name');
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        
        $type = Request::param('type');
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $reports = AnalyticsReport::where($where)
            ->with(['creator'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $reports->total(),
            'data' => $reports->items()
        ]);
    }
    
    public function add($dashboard_id)
    {
        View::assign('dashboard_id', $dashboard_id);
        return View::fetch();
    }
    
    public function save()
    {
        $data = Request::post();
        
        try {
            AnalyticsReport::create([
                'dashboard_id' => $data['dashboard_id'],
                'name' => $data['name'],
                'type' => $data['type'],
                'data_source' => $data['data_source'],
                'query' => $data['query'],
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
        $report = AnalyticsReport::find($id);
        View::assign('report', $report);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $report = AnalyticsReport::find($data['report_id']);
            $report->save([
                'name' => $data['name'],
                'type' => $data['type'],
                'data_source' => $data['data_source'],
                'query' => $data['query'],
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
            AnalyticsReport::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function charts($report_id)
    {
        $report = AnalyticsReport::find($report_id);
        $charts = $report->charts()->select();
        
        return json(['code' => 0, 'msg' => '', 'data' => $charts]);
    }
}