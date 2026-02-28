<?php

namespace app\admin\controller\analytics;

use app\admin\model\AnalyticsVisualization;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Visualization extends BaseController
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
        
        $type = Request::param('type');
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $visualizations = AnalyticsVisualization::where($where)
            ->with(['creator'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $visualizations->total(),
            'data' => $visualizations->items()
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
            AnalyticsVisualization::create([
                'name' => $data['name'],
                'type' => $data['type'],
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
        $visualization = AnalyticsVisualization::find($id);
        View::assign('visualization', $visualization);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $visualization = AnalyticsVisualization::find($data['visualization_id']);
            $visualization->save([
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
            AnalyticsVisualization::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}