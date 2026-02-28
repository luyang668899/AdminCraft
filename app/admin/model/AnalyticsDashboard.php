<?php

namespace app\admin\model;

use think\Model;

class AnalyticsDashboard extends Model
{
    protected $name = 'analytics_dashboard';
    protected $pk = 'dashboard_id';
    
    // 关联报表
    public function reports()
    {
        return $this->hasMany(AnalyticsReport::class, 'dashboard_id', 'dashboard_id');
    }
    
    // 关联创建者
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id', 'id');
    }
}