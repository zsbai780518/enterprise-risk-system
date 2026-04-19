<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 路由配置文件
// +----------------------------------------------------------------------

use think\facade\Route;

// API 路由组
Route::group('api', function () {
    // 用户相关
    Route::post('user/register', 'user/register');      // 用户注册
    Route::post('user/login', 'user/login');            // 用户登录
    Route::post('user/logout', 'user/logout');          // 用户登出
    Route::get('user/info', 'user/info');               // 获取用户信息
    Route::put('user/profile', 'user/profile');         // 更新用户资料
    
    // 企业相关
    Route::get('company/list', 'company/index');        // 企业列表
    Route::get('company/detail', 'company/detail');     // 企业详情
    Route::post('company/search', 'company/search');    // 搜索企业
    Route::post('company/collect', 'company/collect');  // 采集企业数据
    Route::post('company/bind', 'company/bind');        // 绑定企业
    Route::delete('company/unbind', 'company/unbind');  // 解绑企业
    
    // 风险分析
    Route::get('risk/analysis', 'risk/analysis');       // 风险分析
    Route::get('risk/list', 'risk/list');               // 风险列表
    Route::get('risk/statistics', 'risk/statistics');   // 风险统计
    
    // 报告相关
    Route::get('report/list', 'report/index');          // 报告列表
    Route::post('report/generate', 'report/generate');  // 生成报告
    Route::get('report/download', 'report/download');   // 下载报告
    
    // 可视化数据
    Route::get('chart/equity', 'chart/equity');         // 股权结构图数据
    Route::get('chart/risk', 'chart/risk');             // 风险地图数据
    Route::get('chart/qualification', 'chart/qualification'); // 资质合规图谱
});

// 管理后台路由组
Route::group('admin', function () {
    Route::post('login', 'admin/login');                // 管理员登录
    Route::get('dashboard', 'admin/dashboard');         // 管理后台首页
    Route::get('user/list', 'admin/userList');          // 用户列表
    Route::get('company/list', 'admin/companyList');    // 企业列表
    Route::get('system/config', 'admin/config');        // 系统配置
    Route::post('system/config', 'admin/saveConfig');   // 保存配置
});
