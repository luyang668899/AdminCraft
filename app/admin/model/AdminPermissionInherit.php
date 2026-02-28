<?php

namespace app\admin\model;

use think\Model;

class AdminPermissionInherit extends Model
{
    protected $name = 'admin_permission_inherit';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 获取角色继承的权限
     * @param int $groupId 角色ID
     * @return array
     */
    public static function getInheritedPermissions(int $groupId)
    {
        $inheritances = self::where('child_group_id', $groupId)->where('status', 1)->select();
        $inheritedRules = [];
        
        foreach ($inheritances as $inherit) {
            if ($inherit['inherit_type'] == 'all') {
                // 继承父角色所有权限
                $parentGroup = AdminGroup::find($inherit['parent_group_id']);
                if ($parentGroup && $parentGroup['rules'] != '*') {
                    $inheritedRules = array_merge($inheritedRules, explode(',', $parentGroup['rules']));
                }
            } else if ($inherit['inherit_type'] == 'custom' && $inherit['inherit_rules']) {
                // 继承指定权限
                $inheritedRules = array_merge($inheritedRules, explode(',', $inherit['inherit_rules']));
            }
        }
        
        return array_unique($inheritedRules);
    }

    /**
     * 获取角色的完整权限列表（包括继承的）
     * @param int $groupId 角色ID
     * @return array
     */
    public static function getCompletePermissions(int $groupId)
    {
        $group = AdminGroup::find($groupId);
        if (!$group) {
            return [];
        }
        
        if ($group['rules'] == '*') {
            return ['*'];
        }
        
        $ownRules = explode(',', $group['rules']);
        $inheritedRules = self::getInheritedPermissions($groupId);
        
        return array_unique(array_merge($ownRules, $inheritedRules));
    }

    /**
     * 检查角色是否继承了指定权限
     * @param int $groupId 角色ID
     * @param int $ruleId 权限ID
     * @return bool
     */
    public static function hasInheritedPermission(int $groupId, int $ruleId)
    {
        $permissions = self::getCompletePermissions($groupId);
        return in_array('*', $permissions) || in_array((string)$ruleId, $permissions);
    }
}
