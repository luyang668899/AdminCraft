<?php

namespace app\admin\controller\analytics;

use app\admin\model\AnalyticsChart;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Chart extends BaseController
{
    public function index($report_id)
    {
        View::assign('report_id', $report_id);
        return View::fetch();
    }
    
    public function list()
    {
        $report_id = Request::param('report_id');
        $where = ['report_id' => $report_id];
        
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
        
        $charts = AnalyticsChart::where($where)
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $charts->total(),
            'data' => $charts->items()
        ]);
    }
    
    public function add($report_id)
    {
        View::assign('report_id', $report_id);
        return View::fetch();
    }
    
    public function save()
    {
        $data = Request::post();
        
        try {
            AnalyticsChart::create([
                'report_id' => $data['report_id'],
                'name' => $data['name'],
                'type' => $data['type'],
                'config' => $data['config']
            ]);
            
            return json(['code' => 0, 'msg' => '创建成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $chart = AnalyticsChart::find($id);
        View::assign('chart', $chart);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $chart = AnalyticsChart::find($data['chart_id']);
            $chart->save([
                'name' => $data['name'],
                'type' => $data['type'],
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
            AnalyticsChart::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}