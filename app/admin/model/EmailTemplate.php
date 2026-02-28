<?php

namespace app\admin\model;

use think\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_template';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联邮件发送记录
    public function records()
    {
        return $this->hasMany(EmailRecord::class, 'template_id');
    }

    // 根据模板代码获取模板
    public static function getByCode($code)
    {
        return self::where('code', $code)->where('status', 1)->find();
    }

    // 替换模板变量
    public function replaceVariables($variables = [])
    {
        $content = $this->content;
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }
}