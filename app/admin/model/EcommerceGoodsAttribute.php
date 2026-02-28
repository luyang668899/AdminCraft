<?php

namespace app\admin\model;

use think\Model;

class EcommerceGoodsAttribute extends Model
{
    protected $name = 'ecommerce_goods_attribute';
    
    /**
     * 商品关联
     */
    public function goods()
    {
        return $this->belongsTo(EcommerceGoods::class, 'goods_id', 'id');
    }
}