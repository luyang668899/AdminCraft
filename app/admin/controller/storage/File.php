<?php

namespace app\admin\controller\storage;

use app\admin\model\StorageFile;
use think\Controller;
use think\Request;

class File extends Controller
{
    /**
     * 显示文件列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $files = StorageFile::with('config')->select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $files
        ]);
    }

    /**
     * 上传文件
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function upload(Request $request)
    {
        try {
            $file = $request->file('file');
            $configId = $request->param('config_id');
            
            if (!$file) {
                return json([
                    'code' => 1,
                    'msg' => '请选择文件'
                ]);
            }

            $storageFile = StorageFile::upload($file, $configId);
            return json([
                'code' => 0,
                'msg' => '上传成功',
                'data' => $storageFile
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 显示指定的文件
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $file = StorageFile::with('config')->find($id);
        if ($file) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $file
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '文件不存在'
            ]);
        }
    }

    /**
     * 删除文件
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        try {
            $file = StorageFile::find($id);
            if (!$file) {
                return json([
                    'code' => 1,
                    'msg' => '文件不存在'
                ]);
            }

            $file->deleteFile();
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
     * 批量删除文件
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function batchDelete(Request $request)
    {
        try {
            $ids = $request->param('ids');
            if (!is_array($ids)) {
                $ids = explode(',', $ids);
            }

            StorageFile::batchDelete($ids);
            return json([
                'code' => 0,
                'msg' => '批量删除成功'
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 下载文件
     *
     * @param  int  $id
     * @return \think\response\File|
     */
    public function download($id)
    {
        $file = StorageFile::find($id);
        if (!$file) {
            return json([
                'code' => 1,
                'msg' => '文件不存在'
            ]);
        }

        // 这里应该根据存储类型实现不同的下载逻辑
        // 为了演示，我们只返回文件信息
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'url' => $file->url,
                'filename' => $file->original_name
            ]
        ]);
    }
}