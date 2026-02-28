<?php

namespace app\admin\model;

use think\Model;

class AnalyticsReport extends Model
{
    protected $name = 'analytics_report';
    protected $pk = 'report_id';
    
    // 关联仪表盘
    public function dashboard()
    {
        return $this->belongsTo(AnalyticsDashboard::class, 'dashboard_id', 'dashboard_id');
    }
    
    // 关联图表
    public function charts()
    {
        return $this->hasMany(AnalyticsChart::class, 'report_id', 'report_id');
    }
    
    // 关联创建者
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id', 'id');
    }
}