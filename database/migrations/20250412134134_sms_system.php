<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SmsSystem extends Migrator
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
        // 短信配置表
        $table = $this->table('sms_config', ['id' => false, 'primary_key' => 'config_id']);
        $table->addColumn('config_id', 'integer', ['identity' => true, 'comment' => '配置ID'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '短信类型（aliyun、tencent、baidu等）'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '配置名称'])
            ->addColumn('app_id', 'string', ['limit' => 100, 'comment' => '应用ID'])
            ->addColumn('app_secret', 'string', ['limit' => 255, 'comment' => '应用密钥'])
            ->addColumn('sign_name', 'string', ['limit' => 50, 'comment' => '短信签名'])
            ->addColumn('template_id', 'string', ['limit' => 100, 'comment' => '模板ID'])
            ->addColumn('api_url', 'string', ['limit' => 255, 'comment' => 'API地址'])
            ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态（1启用，0禁用）'])
            ->addColumn('config', 'json', ['comment' => '其他配置'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 短信记录表
        $table = $this->table('sms_record', ['id' => false, 'primary_key' => 'record_id']);
        $table->addColumn('record_id', 'integer', ['identity' => true, 'comment' => '记录ID'])
            ->addColumn('config_id', 'integer', ['comment' => '短信配置ID'])
            ->addColumn('mobile', 'string', ['limit' => 20, 'comment' => '手机号码'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '短信类型（验证码、通知等）'])
            ->addColumn('content', 'text', ['comment' => '短信内容'])
            ->addColumn('template_params', 'json', ['comment' => '模板参数'])
            ->addColumn('status', 'integer', ['default' => 0, 'comment' => '状态（0待发送，1发送成功，2发送失败）'])
            ->addColumn('send_time', 'datetime', ['comment' => '发送时间'])
            ->addColumn('error_message', 'text', ['comment' => '错误信息'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 短信验证码表
        $table = $this->table('sms_verification', ['id' => false, 'primary_key' => 'verification_id']);
        $table->addColumn('verification_id', 'integer', ['identity' => true, 'comment' => '验证码ID'])
            ->addColumn('mobile', 'string', ['limit' => 20, 'comment' => '手机号码'])
            ->addColumn('code', 'string', ['limit' => 10, 'comment' => '验证码'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '验证码类型（登录、注册、重置密码等）'])
            ->addColumn('expire_time', 'datetime', ['comment' => '过期时间'])
            ->addColumn('is_used', 'integer', ['default' => 0, 'comment' => '是否使用（0未使用，1已使用）'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
    }
}