<?php

namespace app\admin\model;

use think\Model;

class ContentCategory extends Model
{
    protected $autoWriteTimestamp = true;
    
    protected $append = [
        'parent_name',
    ];
    
    public function getParentNameAttr($value, $row)
    {
        if ($row['pid'] == 0) {
            return '顶级分类';
        }
        $parent = self::where('id', $row['pid'])->value('name');
        return $parent ?: '';
    }
    
    public function children()
    {
        return $this->hasMany(ContentCategory::class, 'pid', 'id');
    }
    
    public function parent()
    {
        return $this->belongsTo(ContentCategory::class, 'pid', 'id');
    }
}