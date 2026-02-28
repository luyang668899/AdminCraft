<?php

namespace app\admin\model;

use think\Model;

class Project extends Model
{
    protected $name = 'project';
    protected $pk = 'project_id';
    
    // 关联任务
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_id', 'project_id');
    }
    
    // 关联项目成员
    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }
    
    // 关联创建者
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id', 'id');
    }
    
    // 获取项目状态文本
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => '待开始',
            1 => '进行中',
            2 => '已完成',
            3 => '已暂停',
            4 => '已取消'
        ];
        return $status[$data['status']] ?? '未知';
    }
    
    // 计算任务完成率
    public function getCompletionRateAttr($value, $data)
    {
        $total = $this->tasks()->count();
        if ($total == 0) {
            return 0;
        }
        $completed = $this->tasks()->where('status', 2)->count();
        return round(($completed / $total) * 100, 2);
    }
}