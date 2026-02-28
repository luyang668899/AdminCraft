<?php

namespace app\admin\model;

use think\Model;

class EcommerceOrderGoods extends Model
{
    protected $name = 'ecommerce_order_goods';
    
    /**
     * 订单关联
     */
    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id', 'id');
    }
    
    /**
     * 商品关联
     */
    public function goods()
    {
        return $this->belongsTo(EcommerceGoods::class, 'goods_id', 'id');
    }
}