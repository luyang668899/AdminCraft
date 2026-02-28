<?php

namespace app\admin\controller\social;

use app\admin\model\SocialConfig;
use think\Controller;
use think\Request;

class Config extends Controller
{
    /**
     * 显示社交媒体配置列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $configs = SocialConfig::select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $configs
        ]);
    }

    /**
     * 显示创建社交媒体配置表单
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
     * 保存新建的社交媒体配置
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $config = new SocialConfig();
        $config->data($data);
        $result = $config->save();

        if ($result) {
            return json([
                'code' => 0,
                'msg' => '保存成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '保存失败'
            ]);
        }
    }

    /**
     * 显示指定的社交媒体配置
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $config = SocialConfig::find($id);
        if ($config) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $config
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }
    }

    /**
     * 显示编辑社交媒体配置表单
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function edit($id)
    {
        $config = SocialConfig::find($id);
        if ($config) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $config
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }
    }

    /**
     * 更新社交媒体配置
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $data = $request->param();
        $config = SocialConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        $result = $config->data($data)->save();
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
     * 删除社交媒体配置
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $config = SocialConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        // 检查是否有用户使用此配置
        if ($config->users->count() > 0) {
            return json([
                'code' => 1,
                'msg' => '该配置下有用户，无法删除'
            ]);
        }

        $result = $config->delete();
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
     * 刷新令牌
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function refreshToken($id)
    {
        $config = SocialConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        $result = $config->refreshToken();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '令牌刷新成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '令牌刷新失败'
            ]);
        }
    }

    /**
     * 获取授权URL
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function getAuthUrl($id)
    {
        $config = SocialConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        $state = $request->param('state', '');
        $authUrl = $config->getAuthUrl($state);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'auth_url' => $authUrl
            ]
        ]);
    }

    /**
     * 回调处理
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function callback(Request $request)
    {
        $code = $request->param('code');
        $state = $request->param('state');
        $platform = $request->param('platform');

        if (!$code || !$platform) {
            return json([
                'code' => 1,
                'msg' => '参数错误'
            ]);
        }

        $config = SocialConfig::getByPlatform($platform);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '平台配置不存在'
            ]);
        }

        $accessToken = $config->getAccessToken($code);
        if ($accessToken) {
            return json([
                'code' => 0,
                'msg' => '授权成功',
                'data' => [
                    'access_token' => $accessToken
                ]
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '授权失败'
            ]);
        }
    }
}