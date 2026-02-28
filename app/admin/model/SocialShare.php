<?php

namespace app\admin\model;

use think\Model;

class SocialShare extends Model
{
    protected $table = 'social_share';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联社交媒体配置
    public function config()
    {
        return $this->belongsTo(SocialConfig::class, 'config_id');
    }

    // 关联系统用户
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 创建分享记录
    public static function createShare($data)
    {
        $share = new self();
        $share->data($data);
        $share->save();
        return $share;
    }

    // 执行分享
    public function doShare()
    {
        $config = $this->config;
        if (!$config || $config->status != 1) {
            $this->status = 2;
            $this->error_msg = '平台配置无效';
            $this->save();
            return false;
        }

        try {
            // 这里应该根据不同平台实现分享逻辑
            // 为了演示，我们只做模拟分享
            
            // 模拟分享成功
            $this->status = 1;
            $this->share_time = time();
            $this->save();
            return true;
        } catch (\Exception $e) {
            $this->status = 2;
            $this->error_msg = $e->getMessage();
            $this->save();
            return false;
        }
    }

    // 获取分享统计
    public static function getShareStats($platform = null, $startTime = null, $endTime = null)
    {
        $query = self::where('status', 1);
        
        if ($platform) {
            $config = SocialConfig::getByPlatform($platform);
            if ($config) {
                $query->where('config_id', $config->id);
            }
        }
        
        if ($startTime) {
            $query->where('share_time', '>=', $startTime);
        }
        
        if ($endTime) {
            $query->where('share_time', '<=', $endTime);
        }
        
        return [
            'total' => $query->count(),
            'platforms' => $query->field('platform, count(*) as count')->group('platform')->select()
        ];
    }
}