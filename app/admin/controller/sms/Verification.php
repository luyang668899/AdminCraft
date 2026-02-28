<?php

namespace app\admin\controller\sms;

use app\admin\model\SmsVerification;
use app\BaseController;
use think\exception\ValidateException;
use think\facade\Request;
use think\facade\View;

class Verification extends BaseController
{
    public function index()
    {
        return View::fetch();
    }
    
    public function list()
    {
        $where = [];
        $mobile = Request::param('mobile');
        if ($mobile) {
            $where[] = ['mobile', 'like', '%' . $mobile . '%'];
        }
        
        $type = Request::param('type');
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        
        $is_used = Request::param('is_used');
        if ($is_used !== null) {
            $where[] = ['is_used', '=', $is_used];
        }
        
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        
        $verifications = SmsVerification::where($where)
            ->order('created_at', 'desc')
            ->paginate(['list_rows' => $limit, 'page' => $page]);
        
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $verifications->total(),
            'data' => $verifications->items()
        ]);
    }
    
    public function delete($id)
    {
        try {
            SmsVerification::destroy($id);
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function sendCode()
    {
        $data = Request::post();
        
        try {
            // 生成验证码
            $code = rand(100000, 999999);
            $expireTime = date('Y-m-d H:i:s', time() + 300); // 5分钟过期
            
            // 保存验证码
            SmsVerification::create([
                'mobile' => $data['mobile'],
                'code' => $code,
                'type' => $data['type'],
                'expire_time' => $expireTime
            ]);
            
            // 这里应该调用短信发送API发送验证码
            // 暂时模拟发送成功
            
            return json(['code' => 0, 'msg' => '验证码发送成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
    
    public function verifyCode()
    {
        $data = Request::post();
        
        try {
            // 查找验证码
            $verification = SmsVerification::where([
                'mobile' => $data['mobile'],
                'code' => $data['code'],
                'type' => $data['type'],
                'is_used' => 0
            ])->order('created_at', 'desc')->find();
            
            if (!$verification) {
                return json(['code' => 1, 'msg' => '验证码错误']);
            }
            
            // 检查是否过期
            if ($verification->expire_time < date('Y-m-d H:i:s')) {
                return json(['code' => 1, 'msg' => '验证码已过期']);
            }
            
            // 标记为已使用
            $verification->markAsUsed();
            
            return json(['code' => 0, 'msg' => '验证码验证成功']);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}