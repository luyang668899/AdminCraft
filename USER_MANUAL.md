# AdminCraft 使用说明书

## 1. 安装指南

### 1.1 环境要求

- **PHP**: 8.1+ 
- **MySQL**: 5.7+ 或 MariaDB 10.2+
- **Web服务器**: Apache 或 Nginx
- **Node.js**: 16+ (前端开发)
- **Composer**: 2.0+ (PHP依赖管理)

### 1.2 安装步骤

#### 1.2.1 克隆代码

```bash
git clone https://github.com/luyang668899/AdminCraft.git
cd AdminCraft
```

#### 1.2.2 安装PHP依赖

```bash
composer install
```

#### 1.2.3 安装前端依赖

```bash
cd web
npm install
```

#### 1.2.4 配置环境

1. 复制环境配置文件
   ```bash
   cp .env-example .env
   ```

2. 编辑 `.env` 文件，配置数据库连接信息
   ```env
   [DATABASE]
   type = mysql
   hostname = localhost
   database = admincraft
   username = root
   password = 123456
   hostport = 3306
   ```

#### 1.2.5 执行安装

1. 访问安装页面
   ```
   http://localhost/AdminCraft/install
   ```

2. 按照安装向导完成安装过程

3. 安装完成后，删除 `public/install` 目录

### 1.3 开发环境配置

#### 1.3.1 启动前端开发服务器

```bash
cd web
npm run dev
```

#### 1.3.2 启动后端服务

```bash
php think run
```

## 2. 功能说明

### 2.1 核心功能

#### 2.1.1 认证管理
- **管理员管理**: 创建、编辑、删除管理员账号
- **角色管理**: 创建和管理角色组，分配权限
- **权限管理**: 配置系统菜单和操作权限
- **数据权限**: 控制用户对数据的访问范围
- **权限继承**: 实现权限的层级继承

#### 2.1.2 内容管理
- **文章管理**: 发布、编辑、删除文章
- **分类管理**: 创建和管理内容分类
- **标签管理**: 创建和管理文章标签

#### 2.1.3 电商系统
- **商品管理**: 添加、编辑、删除商品
- **商品分类**: 创建和管理商品分类
- **订单管理**: 处理用户订单，查看订单详情

#### 2.1.4 CRM系统
- **客户管理**: 管理客户信息，记录客户详情
- **销售漏斗**: 跟踪销售流程，管理销售机会
- **客户跟进**: 记录客户跟进情况，设置跟进提醒

#### 2.1.5 项目管理
- **项目管理**: 创建和管理项目
- **任务管理**: 分配和跟踪任务进度
- **团队协作**: 管理项目成员和协作流程

#### 2.1.6 数据分析
- **仪表盘**: 展示系统关键数据指标
- **报表管理**: 生成和导出各类报表
- **数据可视化**: 通过图表展示数据趋势

#### 2.1.7 第三方集成
- **支付管理**: 配置和管理支付方式
- **短信管理**: 配置短信服务，发送验证码
- **邮件管理**: 配置邮件服务，发送邮件模板
- **云存储**: 配置和管理云存储服务
- **社交媒体**: 集成社交媒体功能

#### 2.1.8 安全管理
- **数据回收**: 管理系统回收站，恢复误删除数据
- **敏感数据**: 保护敏感数据，记录操作日志

#### 2.1.9 系统管理
- **系统配置**: 配置系统参数和设置
- **附件管理**: 管理系统上传的文件
- **个人资料**: 修改管理员个人信息

### 2.2 用户管理

- **用户管理**: 创建和管理系统用户
- **用户组管理**: 创建和管理用户组
- **用户规则**: 配置用户权限规则
- **资金日志**: 记录用户资金变动
- **积分日志**: 记录用户积分变动

## 3. API文档

### 3.1 API基础信息

- **API基础URL**: `http://your-domain/api`
- **请求方式**: POST
- **数据格式**: JSON
- **认证方式**: Token认证

### 3.2 认证API

#### 3.2.1 登录

- **接口**: `/api/account/login`
- **参数**:
  - `username`: 用户名
  - `password`: 密码
  - `captcha`: 验证码
- **返回**:
  ```json
  {
    "code": 1,
    "msg": "登录成功",
    "data": {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
      "admin": {
        "id": 1,
        "username": "admin",
        "nickname": "管理员"
      }
    }
  }
  ```

#### 3.2.2 退出

- **接口**: `/api/account/logout`
- **参数**: 无
- **返回**:
  ```json
  {
    "code": 1,
    "msg": "退出成功"
  }
  ```

### 3.3 其他API

系统提供了丰富的API接口，详细接口文档请参考系统内的API文档模块。

## 4. 开发指南

### 4.1 后端开发

#### 4.1.1 控制器开发

控制器文件位于 `app/admin/controller/` 目录，遵循ThinkPHP的控制器开发规范。

```php
<?php
namespace app\admin\controller;

use app\admin\library\traits\Backend;

class Example extends Backend
{
    public function index()
    {
        // 列表页逻辑
        return $this->view->fetch();
    }
    
    public function add()
    {
        // 添加页逻辑
        return $this->view->fetch();
    }
    
    public function edit($ids = null)
    {
        // 编辑页逻辑
        return $this->view->fetch();
    }
    
    public function delete($ids = null)
    {
        // 删除逻辑
        return json(['code' => 1, 'msg' => '删除成功']);
    }
}
```

#### 4.1.2 模型开发

模型文件位于 `app/admin/model/` 目录，遵循ThinkPHP的模型开发规范。

```php
<?php
namespace app\admin\model;

use think\Model;

class Example extends Model
{
    protected $name = 'example';
    
    // 关联关系
    public function relation()
    {
        return $this->hasOne('Relation');
    }
    
    // 自动完成
    protected $auto = ['create_time'];
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
}
```

#### 4.1.3 验证器开发

验证器文件位于 `app/admin/validate/` 目录，用于验证表单数据。

```php
<?php
namespace app\admin\validate;

use think\Validate;

class Example extends Validate
{
    protected $rule = [
        'name'  => 'require|length:1,50',
        'email' => 'require|email',
    ];
    
    protected $message = [
        'name.require' => '名称不能为空',
        'name.length'  => '名称长度必须在1-50之间',
        'email.require' => '邮箱不能为空',
        'email.email'   => '邮箱格式不正确',
    ];
}
```

### 4.2 前端开发

#### 4.2.1 页面开发

前端页面位于 `web/src/views/` 目录，使用Vue 3 + TypeScript开发。

```vue
<template>
  <div class="example">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>示例页面</span>
          <el-button type="primary" @click="add">添加</el-button>
        </div>
      </template>
      <el-table :data="list" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80"></el-table-column>
        <el-table-column prop="name" label="名称"></el-table-column>
        <el-table-column prop="create_time" label="创建时间"></el-table-column>
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <el-button size="small" @click="edit(scope.row)">编辑</el-button>
            <el-button size="small" type="danger" @click="del(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { getList, deleteItem } from '@/api/backend/example';

const list = ref<any[]>([]);

const loadData = async () => {
  const res = await getList();
  if (res.code === 1) {
    list.value = res.data;
  }
};

const add = () => {
  // 添加逻辑
};

const edit = (row: any) => {
  // 编辑逻辑
};

const del = async (id: number) => {
  const res = await deleteItem(id);
  if (res.code === 1) {
    ElMessage.success('删除成功');
    loadData();
  }
};

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
```

#### 4.2.2 API调用

前端API调用封装在 `web/src/api/` 目录。

```typescript
import request from '@/api/common';

export const getList = () => {
  return request({
    url: '/admin/example/index',
    method: 'post'
  });
};

export const deleteItem = (id: number) => {
  return request({
    url: '/admin/example/delete',
    method: 'post',
    data: { ids: id }
  });
};
```

### 4.3 模块开发

#### 4.3.1 创建模块

1. 在 `modules` 目录下创建模块目录
2. 按照系统规范创建模块文件结构
3. 在系统后台的模块管理中启用模块

#### 4.3.2 插件开发

1. 在 `plugins` 目录下创建插件目录
2. 按照系统规范创建插件文件结构
3. 在系统后台的插件管理中安装和启用插件

## 5. 常见问题

### 5.1 安装问题

**Q: 安装过程中提示数据库连接失败**

A: 请检查数据库配置是否正确，确保数据库服务正常运行，并且数据库用户有足够的权限。

**Q: 安装完成后无法登录**

A: 请检查用户名和密码是否正确，默认管理员账号为 `admin`，密码为 `123456`。

### 5.2 运行问题

**Q: 后台页面无法加载**

A: 请检查前端依赖是否安装完成，尝试重新构建前端资源：
```bash
cd web
npm run build
```

**Q: API接口返回404**

A: 请检查路由配置是否正确，确保控制器和方法存在。

### 5.3 开发问题

**Q: 如何添加新的菜单**

A: 在系统后台的权限管理中添加新的菜单和权限节点。

**Q: 如何自定义主题**

A: 在 `web/src/styles` 目录下修改主题样式，或者在系统后台的配置中选择预设主题。

## 6. 技术支持

- **官方网站**: [https://github.com/luyang668899/AdminCraft](https://github.com/luyang668899/AdminCraft)
- **GitHub Issues**: [https://github.com/luyang668899/AdminCraft/issues](https://github.com/luyang668899/AdminCraft/issues)
- **邮箱支持**: [luyang668899@example.com](mailto:luyang668899@example.com)

## 7. 版本历史

### v1.0.0 (2026-02-28)
- 初始版本
- 完成核心功能开发
- 提供完整的后台管理系统

---

本使用说明书将持续更新，如有任何问题或建议，请联系技术支持。