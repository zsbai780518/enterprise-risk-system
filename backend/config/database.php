<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 数据库配置文件
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'enterprise_risk_system',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => '',
    // 端口
    'hostport'        => '3306',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用 utf8mb4
    'charset'         => 'utf8mb4',
    // 数据库表前缀
    'prefix'          => 'ers_',
    // 数据库部署方式，一般为独立
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 是否需要断线重连
    'break_reconnect' => false,
    // 监听 SQL
    'trigger'         => false,
];
