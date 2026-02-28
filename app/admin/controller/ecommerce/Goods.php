<?php

namespace app\admin\controller\ecommerce;

use app\admin\model\EcommerceGoods;
use app\admin\model\EcommerceGoodsCategory;
use app\admin\library\traits\Backend;
use think\facade\View;

class Goods extends Backend
{
    protected $model = EcommerceGoods::class;
    
    /**
     * 列表页
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->get();
            
            $query = EcommerceGoods::with('category');
            
            // 搜索
            if (isset($params['keyword']) && $params['keyword']) {
                $query->where('name', 'like', '%' . $params['keyword'] . '%')
                      ->whereOr('sn', 'like', '%' . $params['keyword'] . '%');
            }
            
            // 分类筛选
            if (isset($params['category_id']) && $params['category_id']) {
                $query->where('category_id', $params['category_id']);
            }
            
            // 状态筛选
            if (isset($params['status']) && $params['status'] !== '') {
                $query->where('status', $params['status']);
            }
            
            // 分页
            $page = isset($params['page']) ? intval($params['page']) : 1;
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            
            $total = $query->count();
            $list = $query->page($page, $limit)->order('id desc')->select();
            
            return $this->success('获取成功', null, [
                'total' => $total,
                'rows' => $list
            ]);
        }
        
        // 获取分类下拉列表
        $categories = EcommerceGoodsCategory::getSelectList();
        View::assign('categories', $categories);
        
        return View::fetch();
    }
    
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // 处理属性和规格
            $attributes = isset($data['attributes']) ? $data['attributes'] : [];
            $specs = isset($data['specs']) ? $data['specs'] : [];
            unset($data['attributes'], $data['specs']);
            
            // 创建商品
            $goods = new EcommerceGoods();
            $goods->save($data);
            
            // 保存属性
            if (!empty($attributes)) {
                foreach ($attributes as $attr) {
                    if (!empty($attr['name']) && !empty($attr['value'])) {
                        $goods->attributes()->create([
                            'name' => $attr['name'],
                            'value' => $attr['value']
                        ]);
                    }
                }
            }
            
            // 保存规格
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    if (!empty($spec['spec_name']) && !empty($spec['spec_value'])) {
                        $goods->specs()->create([
                            'spec_name' => $spec['spec_name'],
                            'spec_value' => $spec['spec_value'],
                            'price' => $spec['price'] ?? 0,
                            'stock' => $spec['stock'] ?? 0
                        ]);
                    }
                }
            }
            
            return $this->success('添加成功');
        }
        
        // 获取分类下拉列表
        $categories = EcommerceGoodsCategory::getSelectList();
        View::assign('categories', $categories);
        
        return View::fetch();
    }
    
    /**
     * 编辑
     */
    public function edit($id)
    {
        $goods = EcommerceGoods::with(['attributes', 'specs'])->find($id);
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // 处理属性和规格
            $attributes = isset($data['attributes']) ? $data['attributes'] : [];
            $specs = isset($data['specs']) ? $data['specs'] : [];
            unset($data['attributes'], $data['specs']);
            
            // 更新商品
            $goods->save($data);
            
            // 更新属性
            $goods->attributes()->delete();
            if (!empty($attributes)) {
                foreach ($attributes as $attr) {
                    if (!empty($attr['name']) && !empty($attr['value'])) {
                        $goods->attributes()->create([
                            'name' => $attr['name'],
                            'value' => $attr['value']
                        ]);
                    }
                }
            }
            
            // 更新规格
            $goods->specs()->delete();
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    if (!empty($spec['spec_name']) && !empty($spec['spec_value'])) {
                        $goods->specs()->create([
                            'spec_name' => $spec['spec_name'],
                            'spec_value' => $spec['spec_value'],
                            'price' => $spec['price'] ?? 0,
                            'stock' => $spec['stock'] ?? 0
                        ]);
                    }
                }
            }
            
            return $this->success('编辑成功');
        }
        
        // 获取分类下拉列表
        $categories = EcommerceGoodsCategory::getSelectList();
        View::assign('categories', $categories);
        View::assign('goods', $goods);
        
        return View::fetch();
    }
    
    /**
     * 删除
     */
    public function delete($id)
    {
        // 检查是否有关联的订单商品
        $orderGoodsCount = $this->model::find($id)->orderGoods()->count();
        if ($orderGoodsCount > 0) {
            return $this->error('该商品已被订单使用，无法删除');
        }
        
        // 删除商品及其关联数据
        $goods = EcommerceGoods::find($id);
        $goods->attributes()->delete();
        $goods->specs()->delete();
        $result = $goods->delete();
        
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
    
    /**
     * 上架/下架
     */
    public function toggleStatus($id)
    {
        $goods = EcommerceGoods::find($id);
        $goods->status = $goods->status == 1 ? 0 : 1;
        $result = $goods->save();
        
        if ($result) {
            return $this->success('操作成功');
        } else {
            return $this->error('操作失败');
        }
    }
}