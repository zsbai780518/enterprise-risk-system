<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 应用配置文件
// +----------------------------------------------------------------------

return [
    // 应用名称
    'app_name'               => 'enterprise_risk_system',
    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用 Trace
    'app_trace'              => false,
    // 应用日志开关
    'log'                    => true,
    // 应用日志类型
    'log_type'               => 'file',
    // 应用日志路径
    'log_path'               => RUNTIME_PATH . 'log/',
    // 应用日志级别
    'log_level'              => ['error', 'info'],
    // 默认模块
    'default_module'         => 'index',
    // 默认控制器
    'default_controller'     => 'Index',
    // 默认操作
    'default_action'         => 'index',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,
    // 是否支持多模块
    'app_multi_module'       => false,
    // 应用映射目录
    'app_map'                => [],
    // 应用域名绑定
    'domain_bind'            => [],
    // 禁止访问模块列表
    'deny_module_list'       => ['common'],
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',
    // API 返回数据格式
    'default_return_type'    => 'json',
    // 默认异常跳转类型
    'exception_tmpl'         => ROOT_PATH . 'public/error.html',
    // 错误显示信息
    'error_message'          => '页面错误！请稍后再试～',
    // 错误显示方法
    'show_error_msg'         => false,
];
