<?php

namespace app\admin\model;

use think\Model;

class SmsRecord extends Model
{
    protected $name = 'sms_record';
    protected $pk = 'record_id';
    
    // 关联短信配置
    public function config()
    {
        return $this->belongsTo(SmsConfig::class, 'config_id', 'config_id');
    }
    
    // 获取状态文本
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => '待发送',
            1 => '发送成功',
            2 => '发送失败'
        ];
        return $status[$data['status']] ?? '未知';
    }
}