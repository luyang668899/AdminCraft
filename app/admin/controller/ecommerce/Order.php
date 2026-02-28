<?php

namespace app\admin\controller\ecommerce;

use app\admin\model\EcommerceOrder;
use app\admin\library\traits\Backend;
use think\facade\View;

class Order extends Backend
{
    protected $model = EcommerceOrder::class;
    
    /**
     * 列表页
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = EcommerceOrder::with('orderGoods');
            
            // 搜索
            if (isset($params['keyword']) && $params['keyword']) {
                $query->where('order_sn', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('shipping_name', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('shipping_phone', 'like', '%' . $params['keyword'] . '%');
            }
            
            // 订单状态筛选
            if (isset($params['order_status']) && $params['order_status']) {
                $query->where('order_status', $params['order_status']);
            }
            
            // 支付状态筛选
            if (isset($params['payment_status']) && $params['payment_status'] !== '') {
                $query->where('payment_status', $params['payment_status']);
            }
            
            // 时间筛选
            if (isset($params['start_time']) && $params['start_time']) {
                $query->where('create_time', '>=', $params['start_time']);
            }
            if (isset($params['end_time']) && $params['end_time']) {
                $query->where('create_time', '<=', $params['end_time']);
            }
            
            // 分页
            $page = isset($params['page']) ? intval($params['page']) : 1;
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            
            $total = $query->count();
            $list = $query->page($page, $limit)->order('id desc')->select();
            
            return $this->success('获取成功', null, [
                'total' => $total,
                'rows' => $list
            ]);
        }
        
        return View::fetch();
    }
    
    /**
     * 查看订单详情
     */
    public function view($id)
    {
        $order = EcommerceOrder::with(['orderGoods', 'paymentLogs'])->find($id);
        View::assign('order', $order);
        return View::fetch();
    }
    
    /**
     * 更新订单状态
     */
    public function updateStatus($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $order = EcommerceOrder::find($id);
            $order->order_status = $data['order_status'];
            $result = $order->save();
            
            if ($result) {
                return $this->success('操作成功');
            } else {
                return $this->error('操作失败');
            }
        }
    }
    
    /**
     * 删除订单
     */
    public function delete($id)
    {
        $order = EcommerceOrder::find($id);
        
        // 检查订单状态
        if ($order->payment_status == 1) {
            return $this->error('已支付的订单不能删除');
        }
        
        // 删除订单及其关联数据
        $order->orderGoods()->delete();
        $order->paymentLogs()->delete();
        $result = $order->delete();
        
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}