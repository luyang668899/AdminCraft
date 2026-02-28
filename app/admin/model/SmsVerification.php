<?php

namespace app\admin\model;

use think\Model;

class SmsVerification extends Model
{
    protected $name = 'sms_verification';
    protected $pk = 'verification_id';
    
    // 获取是否使用文本
    public function getIsUsedTextAttr($value, $data)
    {
        return $data['is_used'] ? '已使用' : '未使用';
    }
    
    // 检查验证码是否有效
    public function isValid($code, $type)
    {
        return $this->code === $code && $this->type === $type && $this->is_used === 0 && $this->expire_time > date('Y-m-d H:i:s');
    }
    
    // 标记验证码为已使用
    public function markAsUsed()
    {
        $this->is_used = 1;
        return $this->save();
    }
}