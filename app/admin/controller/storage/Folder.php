<?php

namespace app\admin\controller\storage;

use app\admin\model\StorageFolder;
use think\Controller;
use think\Request;

class Folder extends Controller
{
    /**
     * 显示文件夹列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $folders = StorageFolder::getTree();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $folders
        ]);
    }

    /**
     * 创建文件夹
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function create(Request $request)
    {
        try {
            $name = $request->param('name');
            $parentId = $request->param('parent_id', 0);

            if (!$name) {
                return json([
                    'code' => 1,
                    'msg' => '请输入文件夹名称'
                ]);
            }

            $folder = StorageFolder::createFolder($name, $parentId);
            return json([
                'code' => 0,
                'msg' => '创建成功',
                'data' => $folder
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 显示指定的文件夹
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $folder = StorageFolder::with('children')->find($id);
        if ($folder) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $folder
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '文件夹不存在'
            ]);
        }
    }

    /**
     * 更新文件夹
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $folder = StorageFolder::find($id);
        if (!$folder) {
            return json([
                'code' => 1,
                'msg' => '文件夹不存在'
            ]);
        }

        $name = $request->param('name');
        if (!$name) {
            return json([
                'code' => 1,
                'msg' => '请输入文件夹名称'
            ]);
        }

        $folder->name = $name;
        // 更新路径
        $parentPath = '';
        if ($folder->parent_id > 0) {
            $parent = StorageFolder::find($folder->parent_id);
            if ($parent) {
                $parentPath = $parent->path;
            }
        }
        $folder->path = rtrim($parentPath, '/') . '/' . $name;
        $result = $folder->save();

        if ($result) {
            return json([
                'code' => 0,
                'msg' => '更新成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '更新失败'
            ]);
        }
    }

    /**
     * 删除文件夹
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        try {
            $folder = StorageFolder::find($id);
            if (!$folder) {
                return json([
                    'code' => 1,
                    'msg' => '文件夹不存在'
                ]);
            }

            $folder->deleteFolder();
            return json([
                'code' => 0,
                'msg' => '删除成功'
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 获取文件夹树
     *
     * @return \think\response\Json
     */
    public function tree()
    {
        $tree = StorageFolder::getTree();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $tree
        ]);
    }
}