<?php

namespace app\admin\model;

use think\Model;

class EcommerceGoodsSpec extends Model
{
    protected $name = 'ecommerce_goods_spec';
    
    /**
     * 商品关联
     */
    public function goods()
    {
        return $this->belongsTo(EcommerceGoods::class, 'goods_id', 'id');
    }
}