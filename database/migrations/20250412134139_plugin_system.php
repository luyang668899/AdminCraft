<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PluginSystem extends Migrator
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
        // 插件表
        $pluginTable = $this->table('plugin', ['comment' => '插件表']);
        $pluginTable->addColumn('name', 'string', ['limit' => 100, 'comment' => '插件名称'])
                   ->addColumn('title', 'string', ['limit' => 200, 'comment' => '插件标题'])
                   ->addColumn('description', 'text', ['comment' => '插件描述'])
                   ->addColumn('version', 'string', ['limit' => 50, 'comment' => '插件版本'])
                   ->addColumn('author', 'string', ['limit' => 100, 'comment' => '插件作者'])
                   ->addColumn('website', 'string', ['limit' => 255, 'null' => true, 'comment' => '插件网站'])
                   ->addColumn('status', 'boolean', ['default' => 0, 'comment' => '插件状态：0-未安装，1-已安装，2-已启用'])
                   ->addColumn('config', 'text', ['null' => true, 'comment' => '插件配置'])
                   ->addColumn('install_time', 'datetime', ['null' => true, 'comment' => '安装时间'])
                   ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '更新时间'])
                   ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
                   ->addIndex(['name'], ['unique' => true])
                   ->create();
        
        // 插件钩子表
        $hookTable = $this->table('plugin_hook', ['comment' => '插件钩子表']);
        $hookTable->addColumn('name', 'string', ['limit' => 100, 'comment' => '钩子名称'])
                  ->addColumn('title', 'string', ['limit' => 200, 'comment' => '钩子标题'])
                  ->addColumn('description', 'text', ['comment' => '钩子描述'])
                  ->addColumn('type', 'string', ['limit' => 50, 'default' => 'app', 'comment' => '钩子类型：app-应用内，api-API'])
                  ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                  ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
                  ->addIndex(['name'], ['unique' => true])
                  ->create();
        
        // 插件钩子关联表
        $hookPluginTable = $this->table('plugin_hook_plugin', ['comment' => '插件钩子关联表']);
        $hookPluginTable->addColumn('hook_id', 'integer', ['comment' => '钩子ID'])
                        ->addColumn('plugin_name', 'string', ['limit' => 100, 'comment' => '插件名称'])
                        ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                        ->addColumn('sort', 'integer', ['default' => 0, 'comment' => '排序'])
                        ->addIndex(['hook_id', 'plugin_name'], ['unique' => true])
                        ->create();
        
        // 插件市场表
        $marketTable = $this->table('plugin_market', ['comment' => '插件市场表']);
        $marketTable->addColumn('name', 'string', ['limit' => 100, 'comment' => '插件名称'])
                    ->addColumn('title', 'string', ['limit' => 200, 'comment' => '插件标题'])
                    ->addColumn('description', 'text', ['comment' => '插件描述'])
                    ->addColumn('version', 'string', ['limit' => 50, 'comment' => '插件版本'])
                    ->addColumn('author', 'string', ['limit' => 100, 'comment' => '插件作者'])
                    ->addColumn('website', 'string', ['limit' => 255, 'null' => true, 'comment' => '插件网站'])
                    ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0, 'comment' => '插件价格'])
                    ->addColumn('downloads', 'integer', ['default' => 0, 'comment' => '下载次数'])
                    ->addColumn('rating', 'decimal', ['precision' => 3, 'scale' => 1, 'default' => 0, 'comment' => '评分'])
                    ->addColumn('category', 'string', ['limit' => 100, 'comment' => '插件分类'])
                    ->addColumn('compatibility', 'string', ['limit' => 255, 'comment' => '兼容性'])
                    ->addColumn('last_update', 'datetime', ['comment' => '最后更新时间'])
                    ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态'])
                    ->addIndex(['name'], ['unique' => true])
                    ->create();
    }
}
