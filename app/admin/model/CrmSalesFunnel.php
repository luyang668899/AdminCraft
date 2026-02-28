<?php

namespace app\admin\model;

use think\Model;

class CrmSalesFunnel extends Model
{
    protected $name = 'crm_sales_funnel';
    
    /**
     * 客户关联
     */
    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id', 'id');
    }
    
    /**
     * 阶段历史关联
     */
    public function histories()
    {
        return $this->hasMany(CrmSalesFunnelHistory::class, 'funnel_id', 'id');
    }
    
    /**
     * 阶段文本
     */
    public function getStageTextAttr($value, $data)
    {
        $stage = [
            'lead' => '线索',
            'opportunity' => '机会',
            'proposal' => '提案',
            'negotiation' => '谈判',
            'closed' => '成交'
        ];
        return $stage[$data['stage']] ?? '';
    }
    
    /**
     * 状态文本
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            'active' => '进行中',
            'won' => '已赢单',
            'lost' => '已输单'
        ];
        return $status[$data['status']] ?? '';
    }
    
    /**
     * 保存前钩子，记录阶段变更
     */
    public function beforeUpdate($data)
    {
        if (isset($data['stage']) && $data['stage'] != $this->stage) {
            CrmSalesFunnelHistory::create([
                'funnel_id' => $this->id,
                'old_stage' => $this->stage,
                'new_stage' => $data['stage'],
                'admin_id' => $this->admin_id ?? 0
            ]);
        }
        return true;
    }
}