<br />
<div align="center">
    <h1 style="font-size: 36px;color: #2c3e50;font-weight: 600;margin: 0 0 6px 0;">AdminCraft</h1>
    <p style="font-size: 17px;color: #6a8bad;margin-bottom: 10px;">基于ThinkPHP 8.1.1和Vue 3.5.13的企业级后台管理系统框架</p>
    <a href="https://github.com/luyang668899/AdminCraft" target="_blank">GitHub仓库</a> |
    <a href="#使用说明">使用说明</a> |
    <a href="#文档">文档</a>
</div>
<br />
<p align="center">
    <a href="https://www.thinkphp.cn/" target="_blank">
        <img src="https://img.shields.io/badge/ThinkPHP-%3E8.1-brightgreen?color=91aac3&labelColor=439EFD" alt="vue">
    </a>
    <a href="https://v3.vuejs.org/" target="_blank">
        <img src="https://img.shields.io/badge/Vue-%3E3.5-brightgreen?color=91aac3&labelColor=439EFD" alt="vue">
    </a>
    <a href="https://element-plus.org/zh-CN/guide/changelog.html" target="_blank">
        <img src="https://img.shields.io/badge/Element--Plus-%3E2.9-brightgreen?color=91aac3&labelColor=439EFD" alt="element plus">
    </a>
    <a href="https://www.tslang.cn/" target="_blank">
        <img src="https://img.shields.io/badge/TypeScript-%3E5.7-blue?color=91aac3&labelColor=439EFD" alt="typescript">
    </a>
    <a href="https://vitejs.dev/" target="_blank">
        <img src="https://img.shields.io/badge/Vite-%3E6.0-blue?color=91aac3&labelColor=439EFD" alt="vite">
    </a>
    <a href="https://pinia.vuejs.org/" target="_blank">
        <img src="https://img.shields.io/badge/Pinia-%3E2.3-blue?color=91aac3&labelColor=439EFD" alt="vite">
    </a>
    <a href="https://gitee.com/wonderful-code/buildadmin/blob/master/LICENSE" target="_blank">
        <img src="https://img.shields.io/badge/Apache2.0-license-blue?color=91aac3&labelColor=439EFD" alt="license">
    </a>
</p>

<br>
<div align="center">
  <img src="https://doc.buildadmin.com/images/readme/dashboard-radius.png" />
</div>
<br>

### 介绍
🌈 基于 Vue3.x + ThinkPHP8 + TypeScript + Vite + Pinia + Element Plus 等流行技术栈的后台管理系统，支持常驻内存运行、可视化 CRUD 代码生成、自带 WEB 终端、自适应多端、同时提供 Web、WebNuxt、Server 端，内置全局数据回收站和字段级数据修改保护、自动注册路由、无限子级权限管理等，无需授权即可免费商用，希望能帮助大家实现快速开发。

✨ 关于 `Star` 的小小期待 ✨

~~文档和演示站的「入场券」是点亮 Star~~ 哈哈哈，开个玩笑，实际上**您不需要任何「门槛」即可访问源码、文档和演示站**，在您丝滑体验文档与强大功能的同时，我们有个温暖的请求 —— 若 `BuildAdmin` 让您眼前一亮，请为我们点亮一颗 `Star`，这将是一次开发者间最浪漫的「确认过眼神」，亦可助我们向本应「自由开放」的开源界证明：优秀的项目我会发自内心的点亮 Star ~（而不是像某些同类产品哪样，将 Star 作为文档或演示站的「强制交换」条件）

### 主要特性
**🚀 CRUD 代码生成：**
图形化拖拽生成后台增删改查代码，自动创建数据表；大气且实用的表格，多达 24 种表单组件支持，行拖拽排序，受权限控制的编辑和删除等等，并支持关联表，可为您节省大量开发时间。

**💥 内置 WEB 终端：**
在后台管理系统领域，我们率先将终端深度集成于系统的 `本地开发环境` 中，它能实现很多理想中的功能，比如：虽然是基于 Vue3 的系统，但在安装时并不需要手动的执行 `npm install` 命令；CRUD 代码生成完毕后，自动调用 `prettier` 格式化代码等。本终端设计上能够调用环境变量中的任意命令，天花板极高，后续将为您提供更多方便、快捷的服务。

**👍 流行且稳定的技术栈：**
除了基于 `ThinkPHP8` 前后端分离架构外，我们的 `Vue3` 使用了 `setup、useTemplateRef` 等，状态管理使用 `Pinia`，并使用了 `TypeScript、Vite` 等可以为你的知识面添砖加瓦的技术栈。使用流行技术栈自然代表本框架兼容相关（Vue3+TP8+PHP8.x）生态，生态系统内数不清的库、包、组件，能够使您的开发事半功倍。

**🎨 模块市场：**
一键安装数据导入导出、短信发送、支付、云存储、富文本编辑器，甚至 CMS、商城、社区、纯前端技术栈的学习案例项目等，随时随地为系统添砖加瓦，系统能够自动维护 `package.json` 和 `composer.json` 并通过内置终端自动完成模块所需依赖的安装。

**🔀 前后端分离：**
项目的 `web` 文件夹内包含： `干净`（不含后端代码）、`完整`（所有前端代码文件均在此内）的前端代码文件，代码和部署均可前后分离，对前端开发者友好，作为纯前端开发者，您可以将 BAdmin 当做学习与资源的社群，本系统可为您准备好案例和模板等所需要的环境，而您只需专注于学习或工作，不需要会任何后端代码！（邀您：[和我们一起](http://qm.qq.com/cgi-bin/qm/qr?_wv=1027&k=paVQA1dlpsVNHTla-ZAts6e4pPK4va9R&authKey=Eto0dq9DOuYldJPl6URFAXXHlG2AFQtPUBxNHEByEiuSg9OraxMniXIaWFt46OKi&noverify=0&group_code=1039646575) ）

**⚡️ 常驻内存：**
系统内置的功能均可常驻内存运行，享受比传统框架快上数十倍的性能提升！目前 [Workerman 模块](https://modules.buildadmin.com/workerman) 可提供框架的常驻内存 `HTTP` 服务，同时该模块还提供了开箱即用的 `WebSocket` 服务。

**🚚 按需加载：**
前端的页面组件和语言包均是在使用到它们时，才从网络异步加载，服务端则是基于 `TP8` 和 `PSR` 规范，天生拥有真正的按需加载能力，所以，您无需考虑 `我并不需要多语言、我并不需要某个后台功能` 这类的问题，不需要不使用或隐藏即可。

**🌴 数据回收与反悔：**
内置全局数据回收站，并且提供字段级数据修改记录和修改对比，随时回滚和还原，安全且无感。

**✨ 高颜值：**
提供三种布局模式，其中默认布局使用无边框设计风格，它并没有强行填满屏幕的每一个缝然后使用边框线进行分隔，所有的功能版块，都像是悬浮在屏幕上的，同时又将屏幕空间及其合理的利用了。

**🔐 权限验证：**
可视化的管理权限，然后根据权限动态的注册路由、菜单、页面、按钮（权限节点）、支持无限父子级权限分组、前后端搭配鉴权，自由分派页面和按钮权限。

**📝 未来可期：**
我们正在持续维护系统，并着手开发更多基础设施模块，按需一键安装，甚至提供开箱即用的各行业完整应用。

**🧱 一举多得：**
后台自适应 PC、平板、手机 等多种场景的支持，轻松应对各种需求。

**💖 其他杂项：**
角色组/管理员/管理员日志、 会员/会员组/会员余额、积分日志、系统配置/控制台/附件管理/个人资料管理等等、更多特性等你探索...

### 使用说明
- **安装指南**：请参考 [USER_MANUAL.md](USER_MANUAL.md) 文件中的安装步骤
- **默认账号**：用户名 `admin`，密码 `123456`
- **开发环境**：需要 PHP 8.1+、MySQL 5.7+、Node.js 16+

### 文档
- **使用说明书**：[USER_MANUAL.md](USER_MANUAL.md) - 详细的系统使用指南
- **框架结构文档**：[PROJECT_FRAMEWORK.md](PROJECT_FRAMEWORK.md) - 系统架构和模块设计

### 重要信息
- **GitHub仓库**：[https://github.com/luyang668899/AdminCraft](https://github.com/luyang668899/AdminCraft)
- **技术支持**：[luyang668899@example.com](mailto:luyang668899@example.com)

### 项目预览
|  |  |
|---------------------|---------------------|
|![登录](https://doc.buildadmin.com/images/readme/login.gif)|![控制台](https://doc.buildadmin.com/images/readme/dashboard.png)|
|![布局配置](https://doc.buildadmin.com/images/readme/layout.png)|![表格](https://doc.buildadmin.com/images/readme/admin.png)|
|![表单](https://doc.buildadmin.com/images/readme/user.png)|![系统配置](https://doc.buildadmin.com/images/readme/config.png)|
|![数据回收规则](https://doc.buildadmin.com/images/readme/data-recycle.png)|![数据回收日志](https://doc.buildadmin.com/images/readme/data-recycle-log.png)|
|![敏感数据](https://doc.buildadmin.com/images/readme/sensitive-data.png)|![菜单](https://doc.buildadmin.com/images/readme/menu.png)|
|![单栏布局](https://doc.buildadmin.com/images/readme/layout-3.png)|![经典布局](https://doc.buildadmin.com/images/readme/layout-2.png)|

### 特别鸣谢
💕 感谢巨人提供肩膀，排名不分先后
- [Thinkphp](http://www.thinkphp.cn/)
- [Vue](https://github.com/vuejs/core)
- [Element Plus](https://github.com/element-plus/element-plus)
- [TypeScript](https://github.com/microsoft/TypeScript)
- [vue-router](https://github.com/vuejs/vue-router-next)
- [vite](https://github.com/vitejs/vite)
- [Pinia](https://github.com/vuejs/pinia)
- [Axios](https://github.com/axios/axios)
- [echarts](https://github.com/apache/echarts)
- [vueuse](https://github.com/vueuse/vueuse)
- [lodash](https://github.com/lodash/lodash)
- [eslint](https://github.com/eslint/eslint)
- [prettier](https://github.com/prettier/prettier)

### 版权信息
🔐 AdminCraft 遵循 `Apache2.0` 开源协议发布，提供无需授权的免费使用。\
本项目包含的第三方源码和二进制文件之版权信息另行标注。

### 支持项目
💕 无需捐赠，如果觉得项目不错，或者已经在使用了，希望你可以去 [Github](https://github.com/luyang668899/AdminCraft) 帮我们点个 ⭐ Star，这将是对我们极大的鼓励与支持。
