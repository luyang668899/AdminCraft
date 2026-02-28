<?php

namespace app\admin\model;

use think\Model;

class SmsConfig extends Model
{
    protected $name = 'sms_config';
    protected $pk = 'config_id';
    
    // 关联短信记录
    public function records()
    {
        return $this->hasMany(SmsRecord::class, 'config_id', 'config_id');
    }
    
    // 获取短信类型文本
    public function getTypeTextAttr($value, $data)
    {
        $type = [
            'aliyun' => '阿里云短信',
            'tencent' => '腾讯云短信',
            'baidu' => '百度云短信'
        ];
        return $type[$data['type']] ?? '未知';
    }
    
    // 获取状态文本
    public function getStatusTextAttr($value, $data)
    {
        return $data['status'] ? '启用' : '禁用';
    }
}