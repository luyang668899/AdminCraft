<?php

namespace app\admin\model;

use think\Model;

class ProjectMember extends Model
{
    protected $name = 'project_member';
    protected $pk = 'member_id';
    
    // 关联项目
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
    
    // 关联用户
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
    
    // 获取角色文本
    public function getRoleTextAttr($value, $data)
    {
        $role = [
            0 => '成员',
            1 => '管理员',
            2 => '创建者'
        ];
        return $role[$data['role']] ?? '未知';
    }
}