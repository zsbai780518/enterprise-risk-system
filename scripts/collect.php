#!/usr/bin/env php
<?php
/**
 * 企业数据采集服务
 * 支持定时采集、批量采集
 */

// 加载 ThinkPHP
define('APP_PATH', __DIR__ . '/../backend/app/');
define('ROOT_PATH', __DIR__ . '/../backend/');

require __DIR__ . '/../backend/vendor/autoload.php';

use app\model\Company;
use app\service\DataCollectionService;

// 命令行参数
$action = $argv[1] ?? 'help';
$companyId = $argv[2] ?? 0;

echo "企业数据采集服务\n";
echo "================\n\n";

switch ($action) {
    case 'single':
        // 单企业采集
        if (!$companyId) {
            echo "用法：php collect.php single <company_id>\n";
            exit(1);
        }
        echo "开始采集企业 ID: $companyId\n";
        // TODO: 实现采集逻辑
        echo "采集完成\n";
        break;
        
    case 'batch':
        // 批量采集
        echo "开始批量采集...\n";
        // TODO: 从 Excel 读取企业列表并采集
        echo "批量采集完成\n";
        break;
        
    case 'scheduled':
        // 定时采集（被 cron 调用）
        echo "执行定时采集任务...\n";
        // TODO: 查询需要定时采集的企业并采集
        echo "定时采集完成\n";
        break;
        
    case 'help':
    default:
        echo "用法:\n";
        echo "  php collect.php single <company_id>  - 采集单个企业\n";
        echo "  php collect.php batch                - 批量采集\n";
        echo "  php collect.php scheduled            - 定时采集任务\n";
        break;
}

echo "\n";
