<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 V1.0 - 入口文件
// +----------------------------------------------------------------------

// 检测 PHP 版本
if (version_compare(PHP_VERSION, '7.2.5', '<')) {
    die('require PHP > 7.2.5 !');
}

// 定义应用路径
define('APP_PATH', __DIR__ . '/app/');
define('ROOT_PATH', __DIR__ . '/');
define('RUNTIME_PATH', __DIR__ . '/runtime/');
define('PUBLIC_PATH', __DIR__ . '/public/');

// 加载框架引导文件
require __DIR__ . '/vendor/autoload.php';

// 创建应用实例并运行
$app = new \think\App();
$app->initialize();
$app->run()->send();
