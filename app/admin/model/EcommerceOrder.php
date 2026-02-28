<?php

namespace app\admin\model;

use think\Model;

class EcommerceOrder extends Model
{
    protected $name = 'ecommerce_order';
    
    /**
     * 订单商品关联
     */
    public function orderGoods()
    {
        return $this->hasMany(EcommerceOrderGoods::class, 'order_id', 'id');
    }
    
    /**
     * 支付记录关联
     */
    public function paymentLogs()
    {
        return $this->hasMany(EcommercePaymentLog::class, 'order_id', 'id');
    }
    
    /**
     * 支付状态文本
     */
    public function getPaymentStatusTextAttr($value, $data)
    {
        $status = [0 => '未支付', 1 => '已支付'];
        return $status[$data['payment_status']] ?? '';
    }
    
    /**
     * 订单状态文本
     */
    public function getOrderStatusTextAttr($value, $data)
    {
        $status = [
            'pending' => '待处理',
            'processing' => '处理中',
            'completed' => '已完成',
            'cancelled' => '已取消'
        ];
        return $status[$data['order_status']] ?? '';
    }
}