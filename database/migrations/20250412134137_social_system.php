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
        // 社交媒体配置表
        $table = $this->table('social_config', ['comment' => '社交媒体配置表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '配置名称'])
              ->addColumn('platform', 'string', ['limit' => 20, 'comment' => '平台类型: wechat, weibo, douyin, qq, facebook, twitter'])
              ->addColumn('app_id', 'string', ['limit' => 100, 'comment' => '应用ID'])
              ->addColumn('app_secret', 'string', ['limit' => 255, 'comment' => '应用密钥'])
              ->addColumn('redirect_uri', 'string', ['limit' => 255, 'comment' => '回调地址'])
              ->addColumn('access_token', 'string', ['limit' => 500, 'comment' => '访问令牌'])
              ->addColumn('refresh_token', 'string', ['limit' => 500, 'comment' => '刷新令牌'])
              ->addColumn('expires_in', 'integer', ['comment' => '过期时间'])
              ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态: 1启用, 0禁用'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 社交媒体用户表
        $table = $this->table('social_user', ['comment' => '社交媒体用户表']);
        $table->addColumn('config_id', 'integer', ['comment' => '配置ID'])
              ->addColumn('openid', 'string', ['limit' => 100, 'comment' => '平台用户ID'])
              ->addColumn('unionid', 'string', ['limit' => 100, 'comment' => '统一用户ID'])
              ->addColumn('nickname', 'string', ['limit' => 50, 'comment' => '昵称'])
              ->addColumn('avatar', 'string', ['limit' => 255, 'comment' => '头像'])
              ->addColumn('gender', 'integer', ['comment' => '性别'])
              ->addColumn('country', 'string', ['limit' => 50, 'comment' => '国家'])
              ->addColumn('province', 'string', ['limit' => 50, 'comment' => '省份'])
              ->addColumn('city', 'string', ['limit' => 50, 'comment' => '城市'])
              ->addColumn('user_id', 'integer', ['comment' => '系统用户ID'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 社交媒体分享记录表
        $table = $this->table('social_share', ['comment' => '社交媒体分享记录表']);
        $table->addColumn('config_id', 'integer', ['comment' => '配置ID'])
              ->addColumn('user_id', 'integer', ['comment' => '用户ID'])
              ->addColumn('content', 'text', ['comment' => '分享内容'])
              ->addColumn('url', 'string', ['limit' => 500, 'comment' => '分享链接'])
              ->addColumn('platform', 'string', ['limit' => 20, 'comment' => '分享平台'])
              ->addColumn('status', 'integer', ['default' => 0, 'comment' => '状态: 0待分享, 1分享成功, 2分享失败'])
              ->addColumn('error_msg', 'text', ['comment' => '错误信息'])
              ->addColumn('share_time', 'integer', ['comment' => '分享时间'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 社交媒体消息表
        $table = $this->table('social_message', ['comment' => '社交媒体消息表']);
        $table->addColumn('config_id', 'integer', ['comment' => '配置ID'])
              ->addColumn('message_type', 'string', ['limit' => 20, 'comment' => '消息类型'])
              ->addColumn('content', 'text', ['comment' => '消息内容'])
              ->addColumn('sender', 'string', ['limit' => 100, 'comment' => '发送者'])
              ->addColumn('receiver', 'string', ['limit' => 100, 'comment' => '接收者'])
              ->addColumn('status', 'integer', ['default' => 0, 'comment' => '状态: 0未读, 1已读'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();
    }
};