<?php

namespace app\admin\model;

use think\Model;

class EcommerceGoods extends Model
{
    protected $name = 'ecommerce_goods';
    
    /**
     * 分类关联
     */
    public function category()
    {
        return $this->belongsTo(EcommerceGoodsCategory::class, 'category_id', 'id');
    }
    
    /**
     * 属性关联
     */
    public function attributes()
    {
        return $this->hasMany(EcommerceGoodsAttribute::class, 'goods_id', 'id');
    }
    
    /**
     * 规格关联
     */
    public function specs()
    {
        return $this->hasMany(EcommerceGoodsSpec::class, 'goods_id', 'id');
    }
    
    /**
     * 订单商品关联
     */
    public function orderGoods()
    {
        return $this->hasMany(EcommerceOrderGoods::class, 'goods_id', 'id');
    }
    
    /**
     * 商品状态文本
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '下架', 1 => '上架'];
        return $status[$data['status']] ?? '';
    }
}