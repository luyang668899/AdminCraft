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
        // 云存储配置表
        $table = $this->table('storage_config', ['comment' => '云存储配置表']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '配置名称'])
              ->addColumn('type', 'string', ['limit' => 20, 'comment' => '存储类型: local, oss, s3, qiniu, cos'])
              ->addColumn('access_key', 'string', ['limit' => 100, 'comment' => '访问密钥'])
              ->addColumn('secret_key', 'string', ['limit' => 100, 'comment' => ' secret密钥'])
              ->addColumn('bucket', 'string', ['limit' => 100, 'comment' => '存储桶'])
              ->addColumn('region', 'string', ['limit' => 50, 'comment' => '区域'])
              ->addColumn('endpoint', 'string', ['limit' => 255, 'comment' => '端点'])
              ->addColumn('domain', 'string', ['limit' => 255, 'comment' => '访问域名'])
              ->addColumn('path_prefix', 'string', ['limit' => 100, 'comment' => '路径前缀'])
              ->addColumn('is_default', 'boolean', ['default' => false, 'comment' => '是否默认'])
              ->addColumn('status', 'integer', ['default' => 1, 'comment' => '状态: 1启用, 0禁用'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 文件表
        $table = $this->table('storage_file', ['comment' => '文件表']);
        $table->addColumn('config_id', 'integer', ['comment' => '配置ID'])
              ->addColumn('filename', 'string', ['limit' => 255, 'comment' => '文件名'])
              ->addColumn('original_name', 'string', ['limit' => 255, 'comment' => '原始文件名'])
              ->addColumn('path', 'string', ['limit' => 255, 'comment' => '文件路径'])
              ->addColumn('url', 'string', ['limit' => 500, 'comment' => '访问URL'])
              ->addColumn('size', 'integer', ['comment' => '文件大小(字节)'])
              ->addColumn('mime_type', 'string', ['limit' => 100, 'comment' => 'MIME类型'])
              ->addColumn('extension', 'string', ['limit' => 20, 'comment' => '文件扩展名'])
              ->addColumn('hash', 'string', ['limit' => 100, 'comment' => '文件哈希值'])
              ->addColumn('user_id', 'integer', ['comment' => '上传用户ID'])
              ->addColumn('upload_time', 'integer', ['comment' => '上传时间'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();

        // 文件夹表
        $table = $this->table('storage_folder', ['comment' => '文件夹表']);
        $table->addColumn('parent_id', 'integer', ['default' => 0, 'comment' => '父文件夹ID'])
              ->addColumn('name', 'string', ['limit' => 100, 'comment' => '文件夹名称'])
              ->addColumn('path', 'string', ['limit' => 255, 'comment' => '文件夹路径'])
              ->addColumn('create_time', 'integer', ['comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['comment' => '更新时间'])
              ->create();
    }
};