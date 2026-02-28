<?php

namespace app\admin\controller\social;

use app\admin\model\SocialShare;
use think\Controller;
use think\Request;

class Share extends Controller
{
    /**
     * 显示社交媒体分享记录列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $shares = SocialShare::with('config', 'user')->select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $shares
        ]);
    }

    /**
     * 显示创建社交媒体分享记录表单
     *
     * @return \think\response\Json
     */
    public function create()
    {
        return json([
            'code' => 0,
            'msg' => 'success'
        ]);
    }

    /**
     * 保存新建的社交媒体分享记录
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $share = SocialShare::createShare($data);
        
        // 自动执行分享
        $share->doShare();

        return json([
            'code' => 0,
            'msg' => '分享任务已创建',
            'data' => $share
        ]);
    }

    /**
     * 显示指定的社交媒体分享记录
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $share = SocialShare::with('config', 'user')->find($id);
        if ($share) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $share
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }
    }

    /**
     * 执行分享
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function doShare($id)
    {
        $share = SocialShare::find($id);
        if (!$share) {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }

        $result = $share->doShare();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '分享成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '分享失败: ' . $share->error_msg
            ]);
        }
    }

    /**
     * 删除社交媒体分享记录
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $share = SocialShare::find($id);
        if (!$share) {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }

        $result = $share->delete();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '删除成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '删除失败'
            ]);
        }
    }

    /**
     * 获取分享统计
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function getStats(Request $request)
    {
        $platform = $request->param('platform');
        $startTime = $request->param('start_time');
        $endTime = $request->param('end_time');

        $stats = SocialShare::getShareStats($platform, $startTime, $endTime);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $stats
        ]);
    }
}