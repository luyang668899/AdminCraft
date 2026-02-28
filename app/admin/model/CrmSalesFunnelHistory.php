<?php

namespace app\admin\model;

use think\Model;

class CrmSalesFunnelHistory extends Model
{
    protected $name = 'crm_sales_funnel_history';
    
    /**
     * 销售漏斗关联
     */
    public function funnel()
    {
        return $this->belongsTo(CrmSalesFunnel::class, 'funnel_id', 'id');
    }
    
    /**
     * 操作人关联
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
    
    /**
     * 旧阶段文本
     */
    public function getOldStageTextAttr($value, $data)
    {
        $stage = [
            'lead' => '线索',
            'opportunity' => '机会',
            'proposal' => '提案',
            'negotiation' => '谈判',
            'closed' => '成交'
        ];
        return $stage[$data['old_stage']] ?? '';
    }
    
    /**
     * 新阶段文本
     */
    public function getNewStageTextAttr($value, $data)
    {
        $stage = [
            'lead' => '线索',
            'opportunity' => '机会',
            'proposal' => '提案',
            'negotiation' => '谈判',
            'closed' => '成交'
        ];
        return $stage[$data['new_stage']] ?? '';
    }
}