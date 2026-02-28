<?php

namespace app\admin\model;

use think\Model;

class SocialMessage extends Model
{
    protected $table = 'social_message';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联社交媒体配置
    public function config()
    {
        return $this->belongsTo(SocialConfig::class, 'config_id');
    }

    // 标记为已读
    public function markAsRead()
    {
        $this->status = 1;
        return $this->save();
    }

    // 标记为未读
    public function markAsUnread()
    {
        $this->status = 0;
        return $this->save();
    }

    // 发送消息
    public function send()
    {
        $config = $this->config;
        if (!$config || $config->status != 1) {
            return false;
        }

        try {
            // 这里应该根据不同平台实现发送消息的逻辑
            // 为了演示，我们只做模拟发送
            
            // 模拟发送成功
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // 获取未读消息数量
    public static function getUnreadCount($configId = null)
    {
        $query = self::where('status', 0);
        if ($configId) {
            $query->where('config_id', $configId);
        }
        return $query->count();
    }

    // 获取消息列表
    public static function getMessages($configId = null, $status = null, $limit = 20, $offset = 0)
    {
        $query = self::with('config');
        
        if ($configId) {
            $query->where('config_id', $configId);
        }
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        return $query->order('create_time desc')->limit($limit)->offset($offset)->select();
    }
}