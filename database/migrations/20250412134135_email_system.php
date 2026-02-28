<?php

use think\migration\Migrator;
use think\migration\db\Column;

return new class extends Migrator
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
        // 邮件配置表
        $table = $this->table('email_config', ['comment' => '邮件配置表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '配置名称'])
              ->addColumn('type', 'string', ['limit' => 20, 'comment' => '邮件类型: smtp, api'])
              ->addColumn('host', 'string', ['limit' => 100, 'comment' => 'SMTP主机'])
              ->addColumn('port', 'integer', ['comment' => 'SMTP端口'])
              ->addColumn('username', 'string', ['limit' => 100, 'comment' => '用户名'])
              ->addColumn('password', 'string', ['limit' => 255, 'comment' => '密码'])
              ->addColumn('from_email', 'string', ['limit' => 100, 'comment' => '发件人邮箱'])
              ->addColumn('from_name', 'string', ['limit' => 50, 'comment' => '发件人名称'])
              ->addColumn('ssl', 'boolean', ['default' => true, 'comment' => '是否使用SSL'])
              ->addColumn('is_default', 'boolean', ['default' => false, 'comment' => '是否默认'])
              ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态: 1启用, 0禁用'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 邮件模板表
        $table = $this->table('email_template', ['comment' => '邮件模板表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '模板名称'])
              ->addColumn('code', 'string', ['limit' => 30, 'comment' => '模板代码'])
              ->addColumn('subject', 'string', ['limit' => 100, 'comment' => '邮件主题'])
              ->addColumn('content', 'text', ['comment' => '邮件内容'])
              ->addColumn('variables', 'json', ['comment' => '变量定义'])
              ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态: 1启用, 0禁用'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 邮件发送记录表
        $table = $this->table('email_record', ['comment' => '邮件发送记录表']);
        $table->addColumn('config_id', 'integer', ['comment' => '配置ID'])
              ->addColumn('template_id', 'integer', ['comment' => '模板ID'])
              ->addColumn('to_email', 'string', ['limit' => 100, 'comment' => '收件人邮箱'])
              ->addColumn('subject', 'string', ['limit' => 100, 'comment' => '邮件主题'])
              ->addColumn('content', 'text', ['comment' => '邮件内容'])
              ->addColumn('variables', 'json', ['comment' => '变量值'])
              ->addColumn('status', 'integer', ['default' => 0, 'comment' => '状态: 0待发送, 1发送成功, 2发送失败'])
              ->addColumn('error_msg', 'text', ['comment' => '错误信息'])
              ->addColumn('send_time', 'integer', ['comment' => '发送时间'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();
    }
};