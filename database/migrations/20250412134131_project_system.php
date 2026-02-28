<?php

use think\migration\Migrator;

class ProjectSystem extends Migrator
{
    /**
     * @throws Throwable
     */
    public function up(): void
    {
        /**
         * 项目表
         */
        if (!$this->hasTable('project_project')) {
            $table = $this->table('project_project', [
                'id'          => false,
                'comment'     => '项目表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '项目名称', 'null' => false])
                ->addColumn('description', 'text', ['comment' => '项目描述', 'null' => false])
                ->addColumn('status', 'string', ['limit' => 20, 'default' => 'pending', 'comment' => '项目状态:pending=待开始,in_progress=进行中,completed=已完成,cancelled=已取消', 'null' => false])
                ->addColumn('start_date', 'date', ['comment' => '开始日期', 'null' => true])
                ->addColumn('end_date', 'date', ['comment' => '结束日期', 'null' => true])
                ->addColumn('progress', 'integer', ['default' => 0, 'signed' => false, 'comment' => '进度(0-100)', 'null' => false])
                ->addColumn('creator_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '创建人ID', 'null' => false])
                ->addColumn('manager_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '负责人ID', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 任务表
         */
        if (!$this->hasTable('project_task')) {
            $table = $this->table('project_task', [
                'id'          => false,
                'comment'     => '任务表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('project_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '项目ID', 'null' => false])
                ->addColumn('parent_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '父任务ID', 'null' => false])
                ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '任务名称', 'null' => false])
                ->addColumn('description', 'text', ['comment' => '任务描述', 'null' => false])
                ->addColumn('status', 'string', ['limit' => 20, 'default' => 'pending', 'comment' => '任务状态:pending=待开始,in_progress=进行中,completed=已完成,cancelled=已取消', 'null' => false])
                ->addColumn('priority', 'string', ['limit' => 20, 'default' => 'medium', 'comment' => '优先级:low=低,medium=中,high=高,urgent=紧急', 'null' => false])
                ->addColumn('assignee_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '负责人ID', 'null' => false])
                ->addColumn('start_date', 'date', ['comment' => '开始日期', 'null' => true])
                ->addColumn('end_date', 'date', ['comment' => '结束日期', 'null' => true])
                ->addColumn('progress', 'integer', ['default' => 0, 'signed' => false, 'comment' => '进度(0-100)', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间', 'null' => false])
                ->create();
        }

        /**
         * 任务评论表
         */
        if (!$this->hasTable('project_task_comment')) {
            $table = $this->table('project_task_comment', [
                'id'          => false,
                'comment'     => '任务评论表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('task_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '任务ID', 'null' => false])
                ->addColumn('admin_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '评论人ID', 'null' => false])
                ->addColumn('content', 'text', ['comment' => '评论内容', 'null' => false])
                ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间', 'null' => false])
                ->create();
        }

        /**
         * 项目成员表
         */
        if (!$this->hasTable('project_member')) {
            $table = $this->table('project_member', [
                'id'          => false,
                'comment'     => '项目成员表',
                'row_format'  => 'DYNAMIC',
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('project_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '项目ID', 'null' => false])
                ->addColumn('admin_id', 'integer', ['default' => 0, 'signed' => false, 'comment' => '成员ID', 'null' => false])
                ->addColumn('role', 'string', ['limit' => 20, 'default' => 'member', 'comment' => '角色:manager=管理员,member=成员', 'null' => false])
                ->addColumn('join_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '加入时间', 'null' => false])
                ->create();
        }
    }

    public function down(): void
    {
        // 回滚操作
        $this->dropTable('project_member');
        $this->dropTable('project_task_comment');
        $this->dropTable('project_task');
        $this->dropTable('project_project');
    }
}