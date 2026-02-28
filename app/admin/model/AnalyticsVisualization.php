<?php

namespace app\admin\model;

use think\Model;

class AnalyticsVisualization extends Model
{
    protected $name = 'analytics_visualization';
    protected $pk = 'visualization_id';
    
    // 关联创建者
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id', 'id');
    }
}