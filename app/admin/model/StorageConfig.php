<?php

namespace app\admin\model;

use think\Model;

class StorageConfig extends Model
{
    protected $table = 'storage_config';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联文件
    public function files()
    {
        return $this->hasMany(StorageFile::class, 'config_id');
    }

    // 获取默认配置
    public static function getDefault()
    {
        return self::where('is_default', 1)->where('status', 1)->find();
    }

    // 设置为默认配置
    public function setAsDefault()
    {
        // 先将所有配置设为非默认
        self::where('is_default', 1)->update(['is_default' => 0]);
        // 再将当前配置设为默认
        $this->is_default = 1;
        return $this->save();
    }

    // 获取存储客户端实例
    public function getClient()
    {
        // 根据不同的存储类型返回对应的客户端实例
        // 这里只是示例，实际使用时需要集成相应的SDK
        switch ($this->type) {
            case 'local':
                // 本地存储
                return new \app\admin\service\storage\LocalStorage($this);
            case 'oss':
                // 阿里云OSS
                return new \app\admin\service\storage\OssStorage($this);
            case 's3':
                // AWS S3
                return new \app\admin\service\storage\S3Storage($this);
            case 'qiniu':
                // 七牛云
                return new \app\admin\service\storage\QiniuStorage($this);
            case 'cos':
                // 腾讯云COS
                return new \app\admin\service\storage\CosStorage($this);
            default:
                return null;
        }
    }
}