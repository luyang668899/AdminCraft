<?php

namespace app\admin\model;

use think\Model;

class PaymentConfig extends Model
{
    protected $name = 'payment_config';
    protected $pk = 'config_id';
    
    // 关联支付记录
    public function records()
    {
        return $this->hasMany(PaymentRecord::class, 'config_id', 'config_id');
    }
    
    // 获取支付类型文本
    public function getTypeTextAttr($value, $data)
    {
        $type = [
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            'paypal' => 'PayPal'
        ];
        return $type[$data['type']] ?? '未知';
    }
    
    // 获取状态文本
    public function getStatusTextAttr($value, $data)
    {
        return $data['status'] ? '启用' : '禁用';
    }
}