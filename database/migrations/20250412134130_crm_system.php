<?php

use think\migration\Migrator;

class CrmSystem extends Migrator
{
    /**
     * @throws Throwable
     */
    public function up(): void
    {
        /**
         * 客户表
         */
        if (!$this->hasTable('crm_customer')) {
            $table = $this->table('crm_customer', [
                'id'          => false,
                'comment'     => '客户表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '客户姓名', 'null' => false])
                ->addColumn('phone', 'string', ['limit' => 20, 'default' => '', 'comment' => '联系电话', 'null' => false])
                ->addColumn('email', 'string', ['limit' => 100, 'default' => '', 'comment' => '电子邮箱', 'null' => false])
                ->addColumn('company', 'string', ['limit' => 100, 'default' => '', 'comment' => '公司名称', 'null' => false])
                ->addColumn('industry', 'string', ['limit' => 50, 'default' => '', 'comment' => '所属行业', 'null' => false])
                ->addColumn('address', 'string', ['limit' => 255, 'default' => '', 'comment' => '联系地址', 'null' => false])
                ->addColumn('status', 'string', ['limit' => 20, 'default' => 'potential', 'comment' => '客户状态:potential=潜在客户,contact=联系中,deal=已成交,lost=已流失', 'null' => false])
                ->addColumn('level', 'string', ['limit' => 20, 'default' => 'normal', 'comment' => '客户等级:normal=普通,important=重要,vip=VIP', 'null' => false])
                ->addColumn('source', 'string', ['limit' => 50, 'default' => '', 'comment' => '客户来源', 'null' => false])
                ->addColumn('remark', 'text', ['comment' => '备注信息', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 客户跟进记录表
         */
        if (!$this->hasTable('crm_customer_follow')) {
            $table = $this->table('crm_customer_follow', [
                'id'          => false,
                'comment'     => '客户跟进记录表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('customer_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '客户ID', 'null' => false])
                ->addColumn('admin_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '跟进人ID', 'null' => false])
                ->addColumn('follow_type', 'string', ['limit' => 20, 'default' => 'call', 'comment' => '跟进方式:call=电话,email=邮件,visit=拜访,other=其他', 'null' => false])
                ->addColumn('content', 'text', ['comment' => '跟进内容', 'null' => false])
                ->addColumn('next_time', 'datetime', ['comment' => '下次跟进时间', 'null' => true])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->create();
        }

        /**
         * 销售漏斗表
         */
        if (!$this->hasTable('crm_sales_funnel')) {
            $table = $this->table('crm_sales_funnel', [
                'id'          => false,
                'comment'     => '销售漏斗表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('customer_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '客户ID', 'null' => false])
                ->addColumn('stage', 'string', ['limit' => 20, 'default' => 'lead', 'comment' => '阶段:lead=线索,opportunity=机会,proposal=提案,negotiation=谈判,closed=成交', 'null' => false])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '预计金额', 'null' => false])
                ->addColumn('probability', 'integer', ['default' => 0, 'signed' => false, 'comment' => '成交概率(0-100)', 'null' => false])
                ->addColumn('expected_close_date', 'date', ['comment' => '预计成交日期', 'null' => true])
                ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active', 'comment' => '状态:active=进行中,won=已赢单,lost=已输单', 'null' => false])
                ->addColumn('remark', 'text', ['comment' => '备注信息', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 销售漏斗阶段历史表
         */
        if (!$this->hasTable('crm_sales_funnel_history')) {
            $table = $this->table('crm_sales_funnel_history', [
                'id'          => false,
                'comment'     => '销售漏斗阶段历史表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('funnel_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '漏斗ID', 'null' => false])
                ->addColumn('old_stage', 'string', ['limit' => 20, 'default' => '', 'comment' => '旧阶段', 'null' => false])
                ->addColumn('new_stage', 'string', ['limit' => 20, 'default' => '', 'comment' => '新阶段', 'null' => false])
                ->addColumn('admin_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '操作人ID', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->create();
        }
    }

    public function down(): void
    {
        // 回滚操作
        $this->dropTable('crm_sales_funnel_history');
        $this->dropTable('crm_sales_funnel');
        $this->dropTable('crm_customer_follow');
        $this->dropTable('crm_customer');
    }
}