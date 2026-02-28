<?php

namespace app\admin\controller\sms;

use app\admin\model\SmsConfig;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Config extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    
    public function list()
    {
        $where = [];
        $type = Request::param('type');
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        
        $name = Request::param('name');
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        
        $status = Request::param('status');
        if ($status !== null) {
            $where[] = ['status', '=', $status];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $configs = SmsConfig::where($where)
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $configs->total(),
            'data' => $configs->items()
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
            SmsConfig::create([
                'type' => $data['type'],
                'name' => $data['name'],
                'app_id' => $data['app_id'],
                'app_secret' => $data['app_secret'],
                'sign_name' => $data['sign_name'],
                'template_id' => $data['template_id'],
                'api_url' => $data['api_url'],
                'status' => $data['status'],
                'config' => $data['config']
            ]);
            
            return json(['code' => 0, 'msg' => '创建成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        $config = SmsConfig::find($id);
        View::assign('config', $config);
        return View::fetch();
    }
    
    public function update()
    {
        $data = Request::post();
        
        try {
            $config = SmsConfig::find($data['config_id']);
            $config->save([
                'type' => $data['type'],
                'name' => $data['name'],
                'app_id' => $data['app_id'],
                'app_secret' => $data['app_secret'],
                'sign_name' => $data['sign_name'],
                'template_id' => $data['template_id'],
                'api_url' => $data['api_url'],
                'status' => $data['status'],
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
            SmsConfig::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function toggleStatus($id)
    {
        try {
            $config = SmsConfig::find($id);
            $config->status = $config->status ? 0 : 1;
            $config->save();
            return json(['code' => 0, 'msg' => '更新状态成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}