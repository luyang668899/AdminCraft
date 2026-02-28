<?php

use think\migration\Migrator;

class ContentSystem extends Migrator
{
    /**
     * @throws Throwable
     */
    public function up(): void
    {
        /**
         * 内容分类表
         */
        if (!$this->hasTable('content_category')) {
            $table = $this->table('content_category', [
                'id'          => false,
                'comment'     => '内容分类表',
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
         * 内容标签表
         */
        if (!$this->hasTable('content_tag')) {
            $table = $this->table('content_tag', [
                'id'          => false,
                'comment'     => '内容标签表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '标签名称', 'null' => false])
                ->addColumn('alias', 'string', ['limit' => 50, 'default' => '', 'comment' => '标签别名', 'null' => false])
                ->addColumn('sort', 'integer', ['default' => 0, 'signed' => false, 'comment' => '排序', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 内容文章表
         */
        if (!$this->hasTable('content_article')) {
            $table = $this->table('content_article', [
                'id'          => false,
                'comment'     => '内容文章表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('category_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '分类ID', 'null' => false])
                ->addColumn('title', 'string', ['limit' => 100, 'default' => '', 'comment' => '文章标题', 'null' => false])
                ->addColumn('content', 'text', ['comment' => '文章内容', 'null' => false])
                ->addColumn('status', 'boolean', ['default' => 0, 'signed' => false, 'comment' => '状态:0=草稿,1=发布', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 文章标签关联表
         */
        if (!$this->hasTable('content_article_tag')) {
            $table = $this->table('content_article_tag', [
                'id'         => false,
                'comment'    => '文章标签关联表',
                'row_format' => 'DYNAMIC',
                'collation'  => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('article_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '文章ID', 'null' => false])
                ->addColumn('tag_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '标签ID', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addIndex(['article_id'], [
                    'type' => 'BTREE',
                ])
                ->addIndex(['tag_id'], [
                    'type' => 'BTREE',
                ])
                ->create();
        }
    }

    public function down(): void
    {
        // 回滚操作
        $this->dropTable('content_article_tag');
        $this->dropTable('content_article');
        $this->dropTable('content_tag');
        $this->dropTable('content_category');
    }
}
