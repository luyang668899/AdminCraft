<?php

namespace app\admin\controller\ecommerce;

use app\admin\model\EcommerceGoodsCategory;
use app\admin\library\traits\Backend;
use think\facade\View;

class Category extends Backend
{
    protected $model = EcommerceGoodsCategory::class;
    
    /**
     * 列表页
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $tree = EcommerceGoodsCategory::getTree();
            return $this->success('获取成功', null, $tree);
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
            
            $category = new EcommerceGoodsCategory();
            $result = $category->save($data);
            
            if ($result) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
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
        $category = EcommerceGoodsCategory::find($id);
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            
            // 防止父级分类设置为自身或子级
            if ($data['pid'] == $id || $this->checkChild($id, $data['pid'])) {
                return $this->error('父级分类不能设置为自身或子级');
            }
            
            $result = $category->save($data);
            
            if ($result) {
                return $this->success('编辑成功');
            } else {
                return $this->error('编辑失败');
            }
        }
        
        // 获取分类下拉列表
        $categories = EcommerceGoodsCategory::getSelectList();
        View::assign('categories', $categories);
        View::assign('category', $category);
        
        return View::fetch();
    }
    
    /**
     * 删除
     */
    public function delete($id)
    {
        // 检查是否有子分类
        $childCount = EcommerceGoodsCategory::where('pid', $id)->count();
        if ($childCount > 0) {
            return $this->error('请先删除子分类');
        }
        
        $result = EcommerceGoodsCategory::destroy($id);
        
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
    
    /**
     * 检查是否为子级分类
     */
    protected function checkChild($id, $pid)
    {
        $children = EcommerceGoodsCategory::where('pid', $id)->column('id');
        
        if (in_array($pid, $children)) {
            return true;
        }
        
        foreach ($children as $child) {
            if ($this->checkChild($child, $pid)) {
                return true;
            }
        }
        
        return false;
    }
}