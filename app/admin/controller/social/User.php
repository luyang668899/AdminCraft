<?php

namespace app\admin\controller\social;

use app\admin\model\SocialUser;
use app\admin\model\SocialConfig;
use think\Controller;
use think\Request;

class User extends Controller
{
    /**
     * 显示社交媒体用户列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $users = SocialUser::with('config', 'user')->select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $users
        ]);
    }

    /**
     * 显示指定的社交媒体用户
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $user = SocialUser::with('config', 'user')->find($id);
        if ($user) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $user
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }
    }

    /**
     * 绑定系统用户
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\response\Json
     */
    public function bindUser(Request $request, $id)
    {
        $userId = $request->param('user_id');
        if (!$userId) {
            return json([
                'code' => 1,
                'msg' => '请输入用户ID'
            ]);
        }

        $socialUser = SocialUser::find($id);
        if (!$socialUser) {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }

        $result = $socialUser->bindUser($userId);
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '绑定成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '绑定失败'
            ]);
        }
    }

    /**
     * 解绑系统用户
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function unbindUser($id)
    {
        $socialUser = SocialUser::find($id);
        if (!$socialUser) {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }

        $result = $socialUser->unbindUser();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '解绑成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '解绑失败'
            ]);
        }
    }

    /**
     * 获取用户信息
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function getUserInfo($id)
    {
        $socialUser = SocialUser::find($id);
        if (!$socialUser) {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }

        $userInfo = $socialUser->getUserInfo();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $userInfo
        ]);
    }

    /**
     * 根据openid获取用户
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function getByOpenid(Request $request)
    {
        $openid = $request->param('openid');
        $platform = $request->param('platform');

        if (!$openid || !$platform) {
            return json([
                'code' => 1,
                'msg' => '参数错误'
            ]);
        }

        $user = SocialUser::getByOpenid($openid, $platform);
        if ($user) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $user
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }
    }

    /**
     * 根据unionid获取用户
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function getByUnionid(Request $request)
    {
        $unionid = $request->param('unionid');

        if (!$unionid) {
            return json([
                'code' => 1,
                'msg' => '参数错误'
            ]);
        }

        $user = SocialUser::getByUnionid($unionid);
        if ($user) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $user
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '用户不存在'
            ]);
        }
    }
}