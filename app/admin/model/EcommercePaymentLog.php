<?php

namespace app\admin\model;

use think\Model;

class EcommercePaymentLog extends Model
{
    protected $name = 'ecommerce_payment_log';
    
    /**
     * 订单关联
     */
    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id', 'id');
    }
    
    /**
     * 支付状态文本
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '失败', 1 => '成功'];
        return $status[$data['status']] ?? '';
    }
}