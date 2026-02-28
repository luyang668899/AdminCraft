<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PaymentSystem extends Migrator
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
        // 支付配置表
        $table = $this->table('payment_config', ['id' => false, 'primary_key' => 'config_id']);
        $table->addColumn('config_id', 'integer', ['identity' => true, 'comment' => '配置ID'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '支付类型（alipay、wechat、paypal）'])
            ->addColumn('name', 'string', ['limit' => 100, 'comment' => '配置名称'])
            ->addColumn('app_id', 'string', ['limit' => 100, 'comment' => '应用ID'])
            ->addColumn('app_secret', 'string', ['limit' => 255, 'comment' => '应用密钥'])
            ->addColumn('public_key', 'text', ['comment' => '公钥'])
            ->addColumn('private_key', 'text', ['comment' => '私钥'])
            ->addColumn('merchant_id', 'string', ['limit' => 100, 'comment' => '商户ID'])
            ->addColumn('gateway_url', 'string', ['limit' => 255, 'comment' => '网关地址'])
            ->addColumn('return_url', 'string', ['limit' => 255, 'comment' => '回调地址'])
            ->addColumn('notify_url', 'string', ['limit' => 255, 'comment' => '通知地址'])
            ->addColumn('currency', 'string', ['limit' => 10, 'default' => 'CNY', 'comment' => '货币类型'])
            ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态（1启用，0禁用）'])
            ->addColumn('config', 'json', ['comment' => '其他配置'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 支付记录表
        $table = $this->table('payment_record', ['id' => false, 'primary_key' => 'record_id']);
        $table->addColumn('record_id', 'integer', ['identity' => true, 'comment' => '记录ID'])
            ->addColumn('order_id', 'string', ['limit' => 100, 'comment' => '订单ID'])
            ->addColumn('config_id', 'integer', ['comment' => '支付配置ID'])
            ->addColumn('payment_type', 'string', ['limit' => 50, 'comment' => '支付类型'])
            ->addColumn('transaction_id', 'string', ['limit' => 255, 'comment' => '交易ID'])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'comment' => '支付金额'])
            ->addColumn('currency', 'string', ['limit' => 10, 'default' => 'CNY', 'comment' => '货币类型'])
            ->addColumn('status', 'integer', ['default' => 0, 'comment' => '状态（0待支付，1已支付，2支付失败，3已退款）'])
            ->addColumn('pay_time', 'datetime', ['comment' => '支付时间'])
            ->addColumn('refund_time', 'datetime', ['comment' => '退款时间'])
            ->addColumn('notify_data', 'text', ['comment' => '通知数据'])
            ->addColumn('return_data', 'text', ['comment' => '返回数据'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->create();
        
        // 支付日志表
        $table = $this->table('payment_log', ['id' => false, 'primary_key' => 'log_id']);
        $table->addColumn('log_id', 'integer', ['identity' => true, 'comment' => '日志ID'])
            ->addColumn('record_id', 'integer', ['comment' => '支付记录ID'])
            ->addColumn('type', 'string', ['limit' => 50, 'comment' => '日志类型'])
            ->addColumn('content', 'text', ['comment' => '日志内容'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->create();
    }
}