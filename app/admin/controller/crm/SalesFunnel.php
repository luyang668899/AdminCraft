<?php

namespace app\admin\controller\crm;

use app\admin\model\CrmSalesFunnel;
use app\admin\model\CrmSalesFunnelHistory;
use app\admin\model\CrmCustomer;
use app\admin\library\traits\Backend;
use think\facade\View;

class SalesFunnel extends Backend
{
    protected $model = CrmSalesFunnel::class;
    
    /**
     * 列表页
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = CrmSalesFunnel::with('customer')->order('id desc');
            
            // 搜索
            if (isset($params['keyword']) && $params['keyword']) {
                $query->whereHas('customer', function($q) use ($params) {
                    $q->where('name', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('company', 'like', '%' . $params['keyword'] . '%');
                });
            }
            
            // 阶段筛选
            if (isset($params['stage']) && $params['stage']) {
                $query->where('stage', $params['stage']);
            }
            
            // 状态筛选
            if (isset($params['status']) && $params['status']) {
                $query->where('status', $params['status']);
            }
            
            // 分页
            $page = isset($params['page']) ? intval($params['page']) : 1;
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            
            $total = $query->count();
            $list = $query->page($page, $limit)->select();
            
            return $this->success('获取成功', null, [
                'total' => $total,
                'rows' => $list
            ]);
        }
        
        return View::fetch();
    }
    
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            $funnel = new CrmSalesFunnel();
            $result = $funnel->save($data);
            
            if ($result) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }
        
        // 获取客户列表
        $customers = CrmCustomer::where('status', 'neq', 'lost')->select();
        View::assign('customers', $customers);
        
        return View::fetch();
    }
    
    /**
     * 编辑
     */
    public function edit($id)
    {
        $funnel = CrmSalesFunnel::with('customer')->find($id);
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['admin_id'] = $this->adminId;
            
            $result = $funnel->save($data);
            
            if ($result) {
                return $this->success('编辑成功');
            } else {
                return $this->error('编辑失败');
            }
        }
        
        // 获取客户列表
        $customers = CrmCustomer::where('status', 'neq', 'lost')->select();
        View::assign('customers', $customers);
        View::assign('funnel', $funnel);
        
        return View::fetch();
    }
    
    /**
     * 删除
     */
    public function delete($id)
    {
        $result = CrmSalesFunnel::destroy($id);
        
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
    
    /**
     * 查看阶段历史
     */
    public function history($funnel_id)
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = CrmSalesFunnelHistory::with('admin')->where('funnel_id', $funnel_id)->order('id desc');
            
            // 分页
            $page = isset($params['page']) ? intval($params['page']) : 1;
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            
            $total = $query->count();
            $list = $query->page($page, $limit)->select();
            
            return $this->success('获取成功', null, [
                'total' => $total,
                'rows' => $list
            ]);
        }
        
        View::assign('funnel_id', $funnel_id);
        return View::fetch();
    }
}