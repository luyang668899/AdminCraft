<?php

namespace app\admin\model;

use think\Model;

class AnalyticsChart extends Model
{
    protected $name = 'analytics_chart';
    protected $pk = 'chart_id';
    
    // 关联报表
    public function report()
    {
        return $this->belongsTo(AnalyticsReport::class, 'report_id', 'report_id');
    }
}