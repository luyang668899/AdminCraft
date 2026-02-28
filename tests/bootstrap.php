<?php

// 加载 Composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 加载 ThinkPHP 框架
require __DIR__ . '/../think';

// 初始化应用
use think\App;

// 创建应用实例
$app = App::create();

// 注册到容器
$app->instance('app', $app);

// 设置测试环境
$app->env->set('APP_ENV', 'testing');
$app->env->set('APP_DEBUG', true);
