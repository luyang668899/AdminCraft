<?php

use think\migration\Migrator;

class EcommerceSystem extends Migrator
{
    /**
     * @throws Throwable
     */
    public function up(): void
    {
        /**
         * 商品分类表
         */
        if (!$this->hasTable('ecommerce_goods_category')) {
            $table = $this->table('ecommerce_goods_category', [
                'id'          => false,
                'comment'     => '商品分类表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('pid', 'integer', ['default' => 0, 'signed' => false, 'comment' => '父级ID', 'null' => false])
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '分类名称', 'null' => false])
                ->addColumn('alias', 'string', ['limit' => 50, 'default' => '', 'comment' => '分类别名', 'null' => false])
                ->addColumn('sort', 'integer', ['default' => 0, 'signed' => false, 'comment' => '排序', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 商品表
         */
        if (!$this->hasTable('ecommerce_goods')) {
            $table = $this->table('ecommerce_goods', [
                'id'          => false,
                'comment'     => '商品表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('category_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '分类ID', 'null' => false])
                ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '商品名称', 'null' => false])
                ->addColumn('sn', 'string', ['limit' => 50, 'default' => '', 'comment' => '商品编号', 'null' => false])
                ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品价格', 'null' => false])
                ->addColumn('stock', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品库存', 'null' => false])
                ->addColumn('sales', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品销量', 'null' => false])
                ->addColumn('status', 'boolean', ['default' => 1, 'signed' => false, 'comment' => '商品状态:0=下架,1=上架', 'null' => false])
                ->addColumn('description', 'text', ['comment' => '商品描述', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 商品属性表
         */
        if (!$this->hasTable('ecommerce_goods_attribute')) {
            $table = $this->table('ecommerce_goods_attribute', [
                'id'          => false,
                'comment'     => '商品属性表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('goods_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品ID', 'null' => false])
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '属性名称', 'null' => false])
                ->addColumn('value', 'string', ['limit' => 255, 'default' => '', 'comment' => '属性值', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 商品规格表
         */
        if (!$this->hasTable('ecommerce_goods_spec')) {
            $table = $this->table('ecommerce_goods_spec', [
                'id'          => false,
                'comment'     => '商品规格表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('goods_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品ID', 'null' => false])
                ->addColumn('spec_name', 'string', ['limit' => 50, 'default' => '', 'comment' => '规格名称', 'null' => false])
                ->addColumn('spec_value', 'string', ['limit' => 50, 'default' => '', 'comment' => '规格值', 'null' => false])
                ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '规格价格', 'null' => false])
                ->addColumn('stock', 'integer', ['default' => 0, 'signed' => false, 'comment' => '规格库存', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 订单表
         */
        if (!$this->hasTable('ecommerce_order')) {
            $table = $this->table('ecommerce_order', [
                'id'          => false,
                'comment'     => '订单表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('order_sn', 'string', ['limit' => 50, 'default' => '', 'comment' => '订单编号', 'null' => false])
                ->addColumn('user_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '用户ID', 'null' => false])
                ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '订单总金额', 'null' => false])
                ->addColumn('payment_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '实付金额', 'null' => false])
                ->addColumn('payment_method', 'string', ['limit' => 50, 'default' => '', 'comment' => '支付方式', 'null' => false])
                ->addColumn('payment_status', 'boolean', ['default' => 0, 'signed' => false, 'comment' => '支付状态:0=未支付,1=已支付', 'null' => false])
                ->addColumn('order_status', 'string', ['limit' => 50, 'default' => 'pending', 'comment' => '订单状态:pending=待处理,processing=处理中,completed=已完成,cancelled=已取消', 'null' => false])
                ->addColumn('shipping_address', 'text', ['comment' => '收货地址', 'null' => false])
                ->addColumn('shipping_phone', 'string', ['limit' => 20, 'default' => '', 'comment' => '收货电话', 'null' => false])
                ->addColumn('shipping_name', 'string', ['limit' => 50, 'default' => '', 'comment' => '收货人姓名', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 订单商品表
         */
        if (!$this->hasTable('ecommerce_order_goods')) {
            $table = $this->table('ecommerce_order_goods', [
                'id'          => false,
                'comment'     => '订单商品表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('order_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '订单ID', 'null' => false])
                ->addColumn('goods_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品ID', 'null' => false])
                ->addColumn('goods_name', 'string', ['limit' => 100, 'default' => '', 'comment' => '商品名称', 'null' => false])
                ->addColumn('goods_sn', 'string', ['limit' => 50, 'default' => '', 'comment' => '商品编号', 'null' => false])
                ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品价格', 'null' => false])
                ->addColumn('quantity', 'integer', ['default' => 0, 'signed' => false, 'comment' => '商品数量', 'null' => false])
                ->addColumn('spec_info', 'string', ['limit' => 255, 'default' => '', 'comment' => '规格信息', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->create();
        }

        /**
         * 支付记录表
         */
        if (!$this->hasTable('ecommerce_payment_log')) {
            $table = $this->table('ecommerce_payment_log', [
                'id'          => false,
                'comment'     => '支付记录表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('order_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '订单ID', 'null' => false])
                ->addColumn('order_sn', 'string', ['limit' => 50, 'default' => '', 'comment' => '订单编号', 'null' => false])
                ->addColumn('payment_method', 'string', ['limit' => 50, 'default' => '', 'comment' => '支付方式', 'null' => false])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '支付金额', 'null' => false])
                ->addColumn('transaction_id', 'string', ['limit' => 100, 'default' => '', 'comment' => '交易ID', 'null' => false])
                ->addColumn('status', 'boolean', ['default' => 0, 'signed' => false, 'comment' => '支付状态:0=失败,1=成功', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->create();
        }
    }

    public function down(): void
    {
        // 回滚操作
        $this->dropTable('ecommerce_payment_log');
        $this->dropTable('ecommerce_order_goods');
        $this->dropTable('ecommerce_order');
        $this->dropTable('ecommerce_goods_spec');
        $this->dropTable('ecommerce_goods_attribute');
        $this->dropTable('ecommerce_goods');
        $this->dropTable('ecommerce_goods_category');
    }
}
