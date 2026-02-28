<?php

namespace app\admin\controller\sms;

use app\admin\model\SmsRecord;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Record extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    
    public function list()
    {
        $where = [];
        $mobile = Request::param('mobile');
        if ($mobile) {
            $where[] = ['mobile', 'like', '%' . $mobile . '%'];
        }
        
        $type = Request::param('type');
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        
        $status = Request::param('status');
        if ($status !== null) {
            $where[] = ['status', '=', $status];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $records = SmsRecord::where($where)
            ->with(['config'])
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $records->total(),
            'data' => $records->items()
        ]);
    }
    
    public function view($id)
    {
        $record = SmsRecord::with(['config'])->find($id);
        View::assign('record', $record);
        return View::fetch();
    }
    
    public function delete($id)
    {
        try {
            SmsRecord::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function send()
    {
        $data = Request::post();
        
        try {
            // 这里应该调用短信发送API
            // 暂时模拟发送成功
            SmsRecord::create([
                'config_id' => $data['config_id'],
                'mobile' => $data['mobile'],
                'type' => $data['type'],
                'content' => $data['content'],
                'template_params' => $data['template_params'],
                'status' => 1,
                'send_time' => date('Y-m-d H:i:s')
            ]);
            
            return json(['code' => 0, 'msg' => '发送成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}