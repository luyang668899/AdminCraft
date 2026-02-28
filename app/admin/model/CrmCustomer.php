<?php

namespace app\admin\model;

use think\Model;

class CrmCustomer extends Model
{
    protected $name = 'crm_customer';
    
    /**
     * 跟进记录关联
     */
    public function follows()
    {
        return $this->hasMany(CrmCustomerFollow::class, 'customer_id', 'id');
    }
    
    /**
     * 销售漏斗关联
     */
    public function funnels()
    {
        return $this->hasMany(CrmSalesFunnel::class, 'customer_id', 'id');
    }
    
    /**
     * 客户状态文本
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            'potential' => '潜在客户',
            'contact' => '联系中',
            'deal' => '已成交',
            'lost' => '已流失'
        ];
        return $status[$data['status']] ?? '';
    }
    
    /**
     * 客户等级文本
     */
    public function getLevelTextAttr($value, $data)
    {
        $level = [
            'normal' => '普通',
            'important' => '重要',
            'vip' => 'VIP'
        ];
        return $level[$data['level']] ?? '';
    }
}