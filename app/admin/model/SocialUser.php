<?php

namespace app\admin\model;

use think\Model;

class SocialUser extends Model
{
    protected $table = 'social_user';
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

    // 根据openid获取用户
    public static function getByOpenid($openid, $platform)
    {
        $config = SocialConfig::getByPlatform($platform);
        if (!$config) {
            return null;
        }
        return self::where('openid', $openid)->where('config_id', $config->id)->find();
    }

    // 根据unionid获取用户
    public static function getByUnionid($unionid)
    {
        return self::where('unionid', $unionid)->find();
    }

    // 绑定系统用户
    public function bindUser($userId)
    {
        $this->user_id = $userId;
        return $this->save();
    }

    // 解绑系统用户
    public function unbindUser()
    {
        $this->user_id = 0;
        return $this->save();
    }

    // 获取用户信息
    public function getUserInfo()
    {
        // 这里应该根据不同平台实现获取用户信息的逻辑
        // 为了演示，我们只返回数据库中的信息
        return [
            'openid' => $this->openid,
            'unionid' => $this->unionid,
            'nickname' => $this->nickname,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city
        ];
    }
}