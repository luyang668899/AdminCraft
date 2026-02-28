<?php

namespace app\admin\model;

use think\Model;

class PaymentRecord extends Model
{
    protected $name = 'payment_record';
    protected $pk = 'record_id';
    
    // 关联支付配置
    public function config()
    {
        return $this->belongsTo(PaymentConfig::class, 'config_id', 'config_id');
    }
    
    // 关联支付日志
    public function logs()
    {
        return $this->hasMany(PaymentLog::class, 'record_id', 'record_id');
    }
    
    // 获取支付类型文本
    public function getPaymentTypeTextAttr($value, $data)
    {
        $type = [
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            'paypal' => 'PayPal'
        ];
        return $type[$data['payment_type']] ?? '未知';
    }
    
    // 获取状态文本
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => '待支付',
            1 => '已支付',
            2 => '支付失败',
            3 => '已退款'
        ];
        return $status[$data['status']] ?? '未知';
    }
}