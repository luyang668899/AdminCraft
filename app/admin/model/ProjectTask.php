<?php

namespace app\admin\model;

use think\Model;

class ProjectTask extends Model
{
    protected $name = 'project_task';
    protected $pk = 'task_id';
    
    // 关联项目
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
    
    // 关联负责人
    public function assignee()
    {
        return $this->belongsTo(Admin::class, 'assignee_id', 'id');
    }
    
    // 关联创建者
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'creator_id', 'id');
    }
    
    // 关联任务评论
    public function comments()
    {
        return $this->hasMany(ProjectTaskComment::class, 'task_id', 'task_id');
    }
    
    // 获取任务状态文本
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
    
    // 获取任务优先级文本
    public function getPriorityTextAttr($value, $data)
    {
        $priority = [
            0 => '低',
            1 => '中',
            2 => '高',
            3 => '紧急'
        ];
        return $priority[$data['priority']] ?? '未知';
    }
}