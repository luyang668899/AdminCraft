<?php

namespace app\admin\model;

use think\Model;

class AdminDataPermission extends Model
{
    protected $name = 'admin_data_permission';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 获取角色的数据权限
     * @param int $groupId 角色ID
     * @param string $tableName 表名
     * @return array
     */
    public static function getGroupPermissions(int $groupId, string $tableName = '')
    {
        $query = self::where('group_id', $groupId)->where('status', 1);
        
        if ($tableName) {
            $query->where('table_name', $tableName);
        }
        
        return $query->select()->toArray();
    }

    /**
     * 应用数据权限到查询
     * @param \think\Query $query 查询对象
     * @param int $groupId 角色ID
     * @param string $tableName 表名
     * @return \think\Query
     */
    public static function applyPermission(&$query, int $groupId, string $tableName)
    {
        $permissions = self::getGroupPermissions($groupId, $tableName);
        
        if (empty($permissions)) {
            return $query;
        }
        
        $query->where(function($q) use ($permissions) {
            foreach ($permissions as $perm) {
                switch ($perm['rule_type']) {
                    case 'equal':
                        $q->whereOr($perm['field_name'], '=', $perm['rule_value']);
                        break;
                    case 'in':
                        $q->whereOr($perm['field_name'], 'in', explode(',', $perm['rule_value']));
                        break;
                    case 'like':
                        $q->whereOr($perm['field_name'], 'like', '%' . $perm['rule_value'] . '%');
                        break;
                    case 'between':
                        $range = explode(',', $perm['rule_value']);
                        if (count($range) == 2) {
                            $q->whereOr($perm['field_name'], 'between', $range);
                        }
                        break;
                }
            }
        });
        
        return $query;
    }
}
