<?php

namespace app\admin\controller\email;

use app\admin\model\EmailTemplate;
use think\Controller;
use think\Request;

class Template extends Controller
{
    /**
     * 显示邮件模板列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $templates = EmailTemplate::select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $templates
        ]);
    }

    /**
     * 显示创建邮件模板表单
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
     * 保存新建的邮件模板
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $template = new EmailTemplate();
        $template->data($data);
        $result = $template->save();

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
     * 显示指定的邮件模板
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $template
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '模板不存在'
            ]);
        }
    }

    /**
     * 显示编辑邮件模板表单
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function edit($id)
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $template
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '模板不存在'
            ]);
        }
    }

    /**
     * 更新邮件模板
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\response\Json
     */
    public function update(Request $request, $id)
    {
        $data = $request->param();
        $template = EmailTemplate::find($id);
        if (!$template) {
            return json([
                'code' => 1,
                'msg' => '模板不存在'
            ]);
        }

        $result = $template->data($data)->save();
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
     * 删除邮件模板
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $template = EmailTemplate::find($id);
        if (!$template) {
            return json([
                'code' => 1,
                'msg' => '模板不存在'
            ]);
        }

        $result = $template->delete();
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
}