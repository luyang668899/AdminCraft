<?php

namespace app\admin\model;

use think\Model;

class EmailConfig extends Model
{
    protected $table = 'email_config';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联邮件发送记录
    public function records()
    {
        return $this->hasMany(EmailRecord::class, 'config_id');
    }

    // 获取默认配置
    public static function getDefault()
    {
        return self::where('is_default', 1)->where('status', 1)->find();
    }

    // 设置为默认配置
    public function setAsDefault()
    {
        // 先将所有配置设为非默认
        self::where('is_default', 1)->update(['is_default' => 0]);
        // 再将当前配置设为默认
        $this->is_default = 1;
        return $this->save();
    }
}