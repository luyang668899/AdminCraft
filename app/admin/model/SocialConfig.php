<?php

namespace app\admin\model;

use think\Model;

class SocialConfig extends Model
{
    protected $table = 'social_config';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 关联社交媒体用户
    public function users()
    {
        return $this->hasMany(SocialUser::class, 'config_id');
    }

    // 关联社交媒体分享记录
    public function shares()
    {
        return $this->hasMany(SocialShare::class, 'config_id');
    }

    // 关联社交媒体消息
    public function messages()
    {
        return $this->hasMany(SocialMessage::class, 'config_id');
    }

    // 获取平台配置
    public static function getByPlatform($platform)
    {
        return self::where('platform', $platform)->where('status', 1)->find();
    }

    // 检查令牌是否过期
    public function isTokenExpired()
    {
        if (!$this->expires_in) {
            return true;
        }
        return time() > $this->expires_in;
    }

    // 刷新令牌
    public function refreshToken()
    {
        // 这里应该根据不同平台实现令牌刷新逻辑
        // 为了演示，我们只做模拟刷新
        $this->access_token = 'new_access_token_' . time();
        $this->expires_in = time() + 7200; // 2小时
        $this->save();
        return true;
    }

    // 获取授权URL
    public function getAuthUrl($state = '')
    {
        switch ($this->platform) {
            case 'wechat':
                return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->app_id . '&redirect_uri=' . urlencode($this->redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=' . $state . '#wechat_redirect';
            case 'weibo':
                return 'https://api.weibo.com/oauth2/authorize?client_id=' . $this->app_id . '&redirect_uri=' . urlencode($this->redirect_uri) . '&response_type=code&state=' . $state;
            case 'douyin':
                return 'https://open.douyin.com/platform/oauth/connect?client_key=' . $this->app_id . '&redirect_uri=' . urlencode($this->redirect_uri) . '&response_type=code&scope=user_info&state=' . $state;
            default:
                return '';
        }
    }

    // 根据code获取访问令牌
    public function getAccessToken($code)
    {
        // 这里应该根据不同平台实现获取令牌的逻辑
        // 为了演示，我们只做模拟获取
        $this->access_token = 'access_token_' . time();
        $this->refresh_token = 'refresh_token_' . time();
        $this->expires_in = time() + 7200; // 2小时
        $this->save();
        return $this->access_token;
    }
}