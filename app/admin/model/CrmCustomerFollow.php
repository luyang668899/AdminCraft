<?php

namespace app\admin\model;

use think\Model;

class CrmCustomerFollow extends Model
{
    protected $name = 'crm_customer_follow';
    
    /**
     * 客户关联
     */
    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id', 'id');
    }
    
    /**
     * 跟进人关联
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
    
    /**
     * 跟进方式文本
     */
    public function getFollowTypeTextAttr($value, $data)
    {
        $type = [
            'call' => '电话',
            'email' => '邮件',
            'visit' => '拜访',
            'other' => '其他'
        ];
        return $type[$data['follow_type']] ?? '';
    }
}