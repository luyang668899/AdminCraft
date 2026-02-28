<?php

namespace app\admin\model;

use think\Model;

class StorageFolder extends Model
{
    protected $table = 'storage_folder';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联父文件夹
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // 关联子文件夹
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // 获取文件夹树结构
    public static function getTree($parentId = 0)
    {
        $folders = self::where('parent_id', $parentId)->select();
        foreach ($folders as $folder) {
            $folder->children = self::getTree($folder->id);
        }
        return $folders;
    }

    // 创建文件夹
    public static function createFolder($name, $parentId = 0)
    {
        // 获取父文件夹路径
        $parentPath = '';
        if ($parentId > 0) {
            $parent = self::find($parentId);
            if ($parent) {
                $parentPath = $parent->path;
            }
        }

        // 生成文件夹路径
        $path = rtrim($parentPath, '/') . '/' . $name;

        // 创建文件夹记录
        $folder = new self();
        $folder->parent_id = $parentId;
        $folder->name = $name;
        $folder->path = $path;
        $folder->save();

        return $folder;
    }

    // 删除文件夹
    public function deleteFolder()
    {
        // 递归删除子文件夹
        $children = $this->children;
        foreach ($children as $child) {
            $child->deleteFolder();
        }

        // 删除当前文件夹
        $this->delete();

        return true;
    }
}