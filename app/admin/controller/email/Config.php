<?php

namespace app\admin\controller\email;

use app\admin\model\EmailConfig;
use think\Controller;
use think\Request;

class Config extends Controller
{
    /**
     * 显示邮件配置列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $configs = EmailConfig::select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $configs
        ]);
    }

    /**
     * 显示创建邮件配置表单
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
     * 保存新建的邮件配置
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $config = new EmailConfig();
        $config->data($data);
        $result = $config->save();

        if ($result) {
            // 如果设置为默认，更新其他配置
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $config->setAsDefault();
            }
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
     * 显示指定的邮件配置
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $config = EmailConfig::find($id);
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
     * 显示编辑邮件配置表单
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function edit($id)
    {
        $config = EmailConfig::find($id);
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
     * 更新邮件配置
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $data = $request->param();
        $config = EmailConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        $result = $config->data($data)->save();
        if ($result) {
            // 如果设置为默认，更新其他配置
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $config->setAsDefault();
            }
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
     * 删除邮件配置
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $config = EmailConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
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
     * 设置默认配置
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function setDefault($id)
    {
        $config = EmailConfig::find($id);
        if (!$config) {
            return json([
                'code' => 1,
                'msg' => '配置不存在'
            ]);
        }

        $result = $config->setAsDefault();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '设置成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '设置失败'
            ]);
        }
    }
}