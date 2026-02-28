<?php

namespace app\admin\controller\email;

use app\admin\model\EmailRecord;
use think\Controller;
use think\Request;

class Record extends Controller
{
    /**
     * 显示邮件发送记录列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $records = EmailRecord::with('config', 'template')->select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $records
        ]);
    }

    /**
     * 显示创建邮件发送记录表单
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
     * 保存新建的邮件发送记录
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $record = new EmailRecord();
        $record->data($data);
        $result = $record->save();

        if ($result) {
            // 自动发送邮件
            $record->send();
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
     * 显示指定的邮件发送记录
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $record = EmailRecord::with('config', 'template')->find($id);
        if ($record) {
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $record
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }
    }

    /**
     * 重新发送邮件
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function resend($id)
    {
        $record = EmailRecord::find($id);
        if (!$record) {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }

        $result = $record->send();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '发送成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '发送失败: ' . $record->error_msg
            ]);
        }
    }

    /**
     * 批量发送邮件
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function batchSend(Request $request)
    {
        $data = $request->param();
        $emails = explode(',', $data['emails']);
        $templateId = $data['template_id'];
        $variables = isset($data['variables']) ? json_decode($data['variables'], true) : [];

        $result = EmailRecord::batchSend($emails, $templateId, $variables);
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '批量发送任务已创建'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '批量发送失败'
            ]);
        }
    }

    /**
     * 删除邮件发送记录
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $record = EmailRecord::find($id);
        if (!$record) {
            return json([
                'code' => 1,
                'msg' => '记录不存在'
            ]);
        }

        $result = $record->delete();
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