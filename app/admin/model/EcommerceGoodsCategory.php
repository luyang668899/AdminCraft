<?php

namespace app\admin\model;

use think\Model;

class EcommerceGoodsCategory extends Model
{
    protected $name = 'ecommerce_goods_category';
    
    /**
     * 父级分类关联
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'pid', 'id');
    }
    
    /**
     * 子级分类关联
     */
    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'id');
    }
    
    /**
     * 获取分类树结构
     */
    public static function getTree($pid = 0)
    {
        $categories = self::where('pid', $pid)->order('sort asc')->select();
        $tree = [];
        
        foreach ($categories as $category) {
            $item = $category->toArray();
            $item['children'] = self::getTree($category['id']);
            $tree[] = $item;
        }
        
        return $tree;
    }
    
    /**
     * 获取分类下拉列表
     */
    public static function getSelectList($pid = 0, $level = 0, $prefix = '├─', &$list = [])
    {
        $categories = self::where('pid', $pid)->order('sort asc')->select();
        
        foreach ($categories as $category) {
            $list[$category['id']] = str_repeat('　', $level) . $prefix . $category['name'];
            self::getSelectList($category['id'], $level + 1, $prefix, $list);
        }
        
        return $list;
    }
}