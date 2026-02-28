<?php

namespace app\admin\model;

use think\Model;

class EmailRecord extends Model
{
    protected $table = 'email_record';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联邮件配置
    public function config()
    {
        return $this->belongsTo(EmailConfig::class, 'config_id');
    }

    // 关联邮件模板
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    // 发送邮件
    public function send()
    {
        $config = $this->config;
        if (!$config || $config->status != 1) {
            $this->status = 2;
            $this->error_msg = '邮件配置无效';
            $this->save();
            return false;
        }

        try {
            // 这里应该集成实际的邮件发送库
            // 例如使用PHPMailer或SwiftMailer
            // 为了演示，我们只做模拟发送
            
            // 模拟发送成功
            $this->status = 1;
            $this->send_time = time();
            $this->save();
            return true;
        } catch (\Exception $e) {
            $this->status = 2;
            $this->error_msg = $e->getMessage();
            $this->save();
            return false;
        }
    }

    // 批量发送邮件
    public static function batchSend($emails, $templateId, $variables = [])
    {
        $template = EmailTemplate::find($templateId);
        if (!$template) {
            return false;
        }

        $config = EmailConfig::getDefault();
        if (!$config) {
            return false;
        }

        $records = [];
        foreach ($emails as $email) {
            $content = $template->replaceVariables($variables);
            $records[] = [
                'config_id' => $config->id,
                'template_id' => $template->id,
                'to_email' => $email,
                'subject' => $template->subject,
                'content' => $content,
                'variables' => json_encode($variables),
                'status' => 0,
                'create_time' => time(),
                'update_time' => time()
            ];
        }

        if (!empty($records)) {
            self::insertAll($records);
        }

        return true;
    }
}