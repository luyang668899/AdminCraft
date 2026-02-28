<?php

namespace app\admin\controller\crm;

use app\admin\model\CrmCustomer;
use app\admin\model\CrmCustomerFollow;
use app\admin\library\traits\Backend;
use think\facade\View;

class Customer extends Backend
{
    protected $model = CrmCustomer::class;
    
    /**
     * 列表页
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = CrmCustomer::with('follows')->order('id desc');
            
            // 搜索
            if (isset($params['keyword']) && $params['keyword']) {
                $query->where('name', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('phone', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('email', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('company', 'like', '%' . $params['keyword'] . '%');
            }
            
            // 状态筛选
            if (isset($params['status']) && $params['status']) {
                $query->where('status', $params['status']);
            }
            
            // 等级筛选
            if (isset($params['level']) && $params['level']) {
                $query->where('level', $params['level']);
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
            
            $customer = new CrmCustomer();
            $result = $customer->save($data);
            
            if ($result) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }
        
        return View::fetch();
    }
    
    /**
     * 编辑
     */
    public function edit($id)
    {
        $customer = CrmCustomer::find($id);
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            $result = $customer->save($data);
            
            if ($result) {
                return $this->success('编辑成功');
            } else {
                return $this->error('编辑失败');
            }
        }
        
        View::assign('customer', $customer);
        return View::fetch();
    }
    
    /**
     * 删除
     */
    public function delete($id)
    {
        // 检查是否有关联的跟进记录
        $followCount = CrmCustomerFollow::where('customer_id', $id)->count();
        if ($followCount > 0) {
            return $this->error('该客户有关联的跟进记录，无法删除');
        }
        
        $result = CrmCustomer::destroy($id);
        
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
    
    /**
     * 查看跟进记录
     */
    public function followList($customer_id)
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = CrmCustomerFollow::with('admin')->where('customer_id', $customer_id)->order('id desc');
            
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
        
        View::assign('customer_id', $customer_id);
        return View::fetch();
    }
    
    /**
     * 添加跟进记录
     */
    public function addFollow()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['admin_id'] = $this->adminId;
            
            $follow = new CrmCustomerFollow();
            $result = $follow->save($data);
            
            if ($result) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }
        
        $customer_id = $this->request->get('customer_id');
        View::assign('customer_id', $customer_id);
        return View::fetch();
    }
}