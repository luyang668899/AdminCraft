<?php

namespace app\admin\controller\payment;

use app\admin\model\PaymentRecord;
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
        $order_id = Request::param('order_id');
        if ($order_id) {
            $where[] = ['order_id', 'like', '%' . $order_id . '%'];
        }
        
        $payment_type = Request::param('payment_type');
        if ($payment_type) {
            $where[] = ['payment_type', '=', $payment_type];
        }
        
        $status = Request::param('status');
        if ($status !== null) {
            $where[] = ['status', '=', $status];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $records = PaymentRecord::where($where)
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
        $record = PaymentRecord::with(['config', 'logs'])->find($id);
        View::assign('record', $record);
        return View::fetch();
    }
    
    public function delete($id)
    {
        try {
            PaymentRecord::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function logs($record_id)
    {
        $record = PaymentRecord::find($record_id);
        $logs = $record->logs()->order('created_at', 'desc')->select();
        
        return json(['code' => 0, 'msg' => '', 'data' => $logs]);
    }
}