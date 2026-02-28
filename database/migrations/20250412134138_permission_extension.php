<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PermissionExtension extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // 权限规则表扩展
        $ruleTable = $this->table('admin_rule');
        $ruleTable->addColumn('description', 'text', ['null' => true, 'comment' => '权限描述'])
                  ->addColumn('is_menu', 'boolean', ['default' => 1, 'comment' => '是否为菜单'])
                  ->addColumn('icon', 'string', ['limit' => 50, 'null' => true, 'comment' => '菜单图标'])
                  ->addColumn('color', 'string', ['limit' => 20, 'null' => true, 'comment' => '菜单颜色'])
                  ->addColumn('target', 'string', ['limit' => 50, 'null' => true, 'comment' => '打开目标'])
                  ->update();
        
        // 角色表扩展
        $groupTable = $this->table('admin_group');
        $groupTable->addColumn('description', 'text', ['null' => true, 'comment' => '角色描述'])
                   ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                   ->addColumn('creator_id', 'integer', ['null' => true, 'comment' => '创建人ID'])
                   ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
                   ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
                   ->update();
        
        // 数据权限表
        $dataPermTable = $this->table('admin_data_permission', ['comment' => '数据权限表']);
        $dataPermTable->addColumn('group_id', 'integer', ['comment' => '角色ID'])
                      ->addColumn('table_name', 'string', ['limit' => 100, 'comment' => '表名'])
                      ->addColumn('field_name', 'string', ['limit' => 100, 'comment' => '字段名'])
                      ->addColumn('rule_type', 'string', ['limit' => 20, 'comment' => '规则类型: equal, in, like, between'])
                      ->addColumn('rule_value', 'text', ['comment' => '规则值'])
                      ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                      ->addColumn('create_time', 'datetime', ['comment' => '创建时间'])
                      ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
                      ->addIndex(['group_id'])
                      ->addIndex(['table_name'])
                      ->create();
        
        // 权限继承表
        $permInheritTable = $this->table('admin_permission_inherit', ['comment' => '权限继承表']);
        $permInheritTable->addColumn('parent_group_id', 'integer', ['comment' => '父角色ID'])
                         ->addColumn('child_group_id', 'integer', ['comment' => '子角色ID'])
                         ->addColumn('inherit_type', 'string', ['limit' => 20, 'default' => 'all', 'comment' => '继承类型: all, custom'])
                         ->addColumn('inherit_rules', 'text', ['null' => true, 'comment' => '继承的规则ID列表'])
                         ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                         ->addColumn('create_time', 'datetime', ['comment' => '创建时间'])
                         ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
                         ->addIndex(['parent_group_id'])
                         ->addIndex(['child_group_id'])
                         ->addUniqueIndex(['parent_group_id', 'child_group_id'])
                         ->create();
    }
}
