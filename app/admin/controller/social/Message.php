<?php

namespace app\admin\controller\social;

use app\admin\model\SocialMessage;
use think\Controller;
use think\Request;

class Message extends Controller
{
    /**
     * 显示社交媒体消息列表
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $messages = SocialMessage::with('config')->select();
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * 显示创建社交媒体消息表单
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
     * 保存新建的社交媒体消息
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function save(Request $request)
    {
        $data = $request->param();
        $message = new SocialMessage();
        $message->data($data);
        $result = $message->save();

        if ($result) {
            // 自动发送消息
            $message->send();
            return json([
                'code' => 0,
                'msg' => '消息已创建',
                'data' => $message
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '创建失败'
            ]);
        }
    }

    /**
     * 显示指定的社交媒体消息
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $message = SocialMessage::with('config')->find($id);
        if ($message) {
            // 标记为已读
            $message->markAsRead();
            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => $message
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '消息不存在'
            ]);
        }
    }

    /**
     * 标记消息为已读
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function markAsRead($id)
    {
        $message = SocialMessage::find($id);
        if (!$message) {
            return json([
                'code' => 1,
                'msg' => '消息不存在'
            ]);
        }

        $result = $message->markAsRead();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '标记成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '标记失败'
            ]);
        }
    }

    /**
     * 标记消息为未读
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function markAsUnread($id)
    {
        $message = SocialMessage::find($id);
        if (!$message) {
            return json([
                'code' => 1,
                'msg' => '消息不存在'
            ]);
        }

        $result = $message->markAsUnread();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '标记成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '标记失败'
            ]);
        }
    }

    /**
     * 发送消息
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function send($id)
    {
        $message = SocialMessage::find($id);
        if (!$message) {
            return json([
                'code' => 1,
                'msg' => '消息不存在'
            ]);
        }

        $result = $message->send();
        if ($result) {
            return json([
                'code' => 0,
                'msg' => '发送成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '发送失败'
            ]);
        }
    }

    /**
     * 删除社交媒体消息
     *
     * @param  int  $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        $message = SocialMessage::find($id);
        if (!$message) {
            return json([
                'code' => 1,
                'msg' => '消息不存在'
            ]);
        }

        $result = $message->delete();
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
     * 获取未读消息数量
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function getUnreadCount(Request $request)
    {
        $configId = $request->param('config_id');
        $count = SocialMessage::getUnreadCount($configId);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'count' => $count
            ]
        ]);
    }

    /**
     * 获取消息列表
     *
     * @param  \think\Request  $request
     * @return \think\response\Json
     */
    public function getMessages(Request $request)
    {
        $configId = $request->param('config_id');
        $status = $request->param('status');
        $limit = $request->param('limit', 20);
        $offset = $request->param('offset', 0);

        $messages = SocialMessage::getMessages($configId, $status, $limit, $offset);
        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => $messages
        ]);
    }
}