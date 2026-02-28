<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AnalyticsSystem extends Migrator
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
        // 数据仪表盘表
        $table = $this->table('analytics_dashboard', ['id' => false, 'primary_key' => 'dashboard_id']);
        $table->addColumn('dashboard_id', 'integer', ['identity' => true, 'comment' => '仪表盘ID'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '仪表盘名称'])
            ->addColumn('description', 'text', ['comment' => '仪表盘描述'])
            ->addColumn('config', 'json', ['comment' => '仪表盘配置'])
            ->addColumn('creator_id', 'integer', ['comment' => '创建者ID'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 数据报表表
        $table = $this->table('analytics_report', ['id' => false, 'primary_key' => 'report_id']);
        $table->addColumn('report_id', 'integer', ['identity' => true, 'comment' => '报表ID'])
            ->addColumn('dashboard_id', 'integer', ['comment' => '所属仪表盘ID'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '报表名称'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '报表类型'])
            ->addColumn('data_source', 'string', ['limit' => 100, 'comment' => '数据源'])
            ->addColumn('query', 'text', ['comment' => '查询语句'])
            ->addColumn('config', 'json', ['comment' => '报表配置'])
            ->addColumn('creator_id', 'integer', ['comment' => '创建者ID'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 数据图表表
        $table = $this->table('analytics_chart', ['id' => false, 'primary_key' => 'chart_id']);
        $table->addColumn('chart_id', 'integer', ['identity' => true, 'comment' => '图表ID'])
            ->addColumn('report_id', 'integer', ['comment' => '所属报表ID'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '图表名称'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '图表类型'])
            ->addColumn('config', 'json', ['comment' => '图表配置'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 数据可视化表
        $table = $this->table('analytics_visualization', ['id' => false, 'primary_key' => 'visualization_id']);
        $table->addColumn('visualization_id', 'integer', ['identity' => true, 'comment' => '可视化ID'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '可视化名称'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '可视化类型'])
            ->addColumn('config', 'json', ['comment' => '可视化配置'])
            ->addColumn('creator_id', 'integer', ['comment' => '创建者ID'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
    }
}