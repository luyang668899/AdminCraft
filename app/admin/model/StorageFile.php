<?php

namespace app\admin\model;

use think\Model;

class StorageFile extends Model
{
    protected $table = 'storage_file';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联存储配置
    public function config()
    {
        return $this->belongsTo(StorageConfig::class, 'config_id');
    }

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 上传文件
    public static function upload($file, $configId = null)
    {
        // 获取存储配置
        if ($configId) {
            $config = StorageConfig::find($configId);
        } else {
            $config = StorageConfig::getDefault();
        }

        if (!$config) {
            throw new \Exception('未找到存储配置');
        }

        // 获取存储客户端
        $client = $config->getClient();
        if (!$client) {
            throw new \Exception('获取存储客户端失败');
        }

        // 生成文件名
        $originalName = $file->getOriginalName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
        $path = $config->path_prefix . '/' . date('Y/m/d') . '/' . $filename;

        // 上传文件
        try {
            $url = $client->upload($file, $path);
            
            // 计算文件哈希值
            $hash = md5_file($file->getRealPath());
            
            // 保存文件记录
            $storageFile = new self();
            $storageFile->config_id = $config->id;
            $storageFile->filename = $filename;
            $storageFile->original_name = $originalName;
            $storageFile->path = $path;
            $storageFile->url = $url;
            $storageFile->size = $file->getSize();
            $storageFile->mime_type = $file->getMime();
            $storageFile->extension = $extension;
            $storageFile->hash = $hash;
            $storageFile->user_id = 1; // 这里应该获取当前登录用户ID
            $storageFile->upload_time = time();
            $storageFile->save();
            
            return $storageFile;
        } catch (\Exception $e) {
            throw new \Exception('上传文件失败: ' . $e->getMessage());
        }
    }

    // 删除文件
    public function deleteFile()
    {
        // 获取存储客户端
        $config = $this->config;
        if (!$config) {
            throw new \Exception('未找到存储配置');
        }

        $client = $config->getClient();
        if (!$client) {
            throw new \Exception('获取存储客户端失败');
        }

        try {
            // 删除存储中的文件
            $client->delete($this->path);
            
            // 删除数据库记录
            $this->delete();
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('删除文件失败: ' . $e->getMessage());
        }
    }

    // 批量删除文件
    public static function batchDelete($ids)
    {
        foreach ($ids as $id) {
            $file = self::find($id);
            if ($file) {
                $file->deleteFile();
            }
        }
        return true;
    }
}