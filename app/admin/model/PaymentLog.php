<?php

namespace app\admin\model;

use think\Model;

class PaymentLog extends Model
{
    protected $name = 'payment_log';
    protected $pk = 'log_id';
    
    // 关联支付记录
    public function record()
    {
        return $this->belongsTo(PaymentRecord::class, 'record_id', 'record_id');
    }
}