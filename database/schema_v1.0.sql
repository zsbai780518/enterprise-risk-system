-- 企业风险分析系统 V1.0 数据库设计
-- 创建时间：2026-04-20
-- 字符集：utf8mb4

-- ============================================
-- 1. 用户表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_user` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '用户 ID',
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT '用户名',
  `password` VARCHAR(255) NOT NULL COMMENT '密码 (bcrypt 加密)',
  `phone` VARCHAR(20) UNIQUE COMMENT '手机号',
  `email` VARCHAR(100) COMMENT '邮箱',
  `wechat_openid` VARCHAR(64) COMMENT '微信 OpenID',
  `role` TINYINT UNSIGNED DEFAULT 1 COMMENT '角色：1=普通用户 2=企业管理员 3=机构管理员 4=超级管理员',
  `status` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态：0=禁用 1=正常',
  `avatar` VARCHAR(255) COMMENT '头像 URL',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `last_login_at` TIMESTAMP NULL COMMENT '最后登录时间',
  INDEX `idx_phone` (`phone`),
  INDEX `idx_username` (`username`),
  INDEX `idx_wechat` (`wechat_openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ============================================
-- 2. 企业表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_company` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '企业 ID',
  `name` VARCHAR(200) NOT NULL COMMENT '企业名称',
  `unified_social_code` VARCHAR(50) UNIQUE COMMENT '统一社会信用代码',
  `registration_number` VARCHAR(50) COMMENT '工商注册号',
  `legal_representative` VARCHAR(50) COMMENT '法定代表人',
  `registered_capital` DECIMAL(20,2) COMMENT '注册资本 (万元)',
  `establishment_date` DATE COMMENT '成立日期',
  `business_status` VARCHAR(50) COMMENT '经营状态',
  `company_type` VARCHAR(50) COMMENT '企业类型',
  `industry` VARCHAR(100) COMMENT '所属行业',
  `address` VARCHAR(500) COMMENT '注册地址',
  `business_scope` TEXT COMMENT '经营范围',
  `website` VARCHAR(255) COMMENT '官网',
  `phone` VARCHAR(50) COMMENT '企业电话',
  `email` VARCHAR(100) COMMENT '企业邮箱',
  `risk_score` DECIMAL(5,2) DEFAULT 0 COMMENT '风险评分 (0-100)',
  `risk_level` TINYINT UNSIGNED DEFAULT 1 COMMENT '风险等级：1=低 2=中 3=高',
  `data_updated_at` TIMESTAMP NULL COMMENT '数据更新时间',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_name` (`name`),
  INDEX `idx_uscc` (`unified_social_code`),
  INDEX `idx_risk_level` (`risk_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业基本信息表';

-- ============================================
-- 3. 用户企业绑定表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_user_company` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '绑定 ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `is_owner` TINYINT UNSIGNED DEFAULT 0 COMMENT '是否为企业所有者：0=否 1=是',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '绑定时间',
  UNIQUE KEY `uk_user_company` (`user_id`, `company_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_company` (`company_id`),
  FOREIGN KEY (`user_id`) REFERENCES `ers_user`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户企业绑定关系表';

-- ============================================
-- 4. 股东信息表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_shareholder` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '股东 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `shareholder_name` VARCHAR(200) NOT NULL COMMENT '股东名称',
  `shareholder_type` TINYINT UNSIGNED DEFAULT 1 COMMENT '股东类型：1=自然人 2=企业',
  `capital_contribution` DECIMAL(20,2) COMMENT '认缴出资额 (万元)',
  `capital_ratio` DECIMAL(5,2) COMMENT '持股比例 (%)',
  `contribution_date` DATE COMMENT '认缴出资日期',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_company` (`company_id`),
  INDEX `idx_shareholder` (`shareholder_name`),
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业股东信息表';

-- ============================================
-- 5. 风险信息表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_risk` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '风险 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `risk_type` TINYINT UNSIGNED NOT NULL COMMENT '风险类型：1=工商 2=法律诉讼 3=资质合规 4=知识产权 5=舆情 6=经营',
  `risk_level` TINYINT UNSIGNED NOT NULL COMMENT '风险等级：1=低 2=中 3=高',
  `risk_title` VARCHAR(200) NOT NULL COMMENT '风险标题',
  `risk_content` TEXT COMMENT '风险详情',
  `risk_source` VARCHAR(255) COMMENT '风险来源',
  `publish_date` DATE COMMENT '发布日期',
  `amount` DECIMAL(20,2) COMMENT '涉及金额 (元)',
  `status` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态：0=已处理 1=未处理',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_company` (`company_id`),
  INDEX `idx_type` (`risk_type`),
  INDEX `idx_level` (`risk_level`),
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业风险信息表';

-- ============================================
-- 6. 资质许可表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_qualification` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '资质 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `qual_name` VARCHAR(200) NOT NULL COMMENT '资质名称',
  `qual_type` VARCHAR(50) COMMENT '资质类型',
  `cert_number` VARCHAR(100) COMMENT '证书编号',
  `issue_authority` VARCHAR(200) COMMENT '发证机关',
  `issue_date` DATE COMMENT '发证日期',
  `valid_from` DATE COMMENT '有效期开始',
  `valid_to` DATE COMMENT '有效期结束',
  `status` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态：0=过期 1=有效 2=缺失 (推荐)',
  `is_required` TINYINT UNSIGNED DEFAULT 0 COMMENT '是否行业必备资质：0=否 1=是',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_company` (`company_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_valid_to` (`valid_to`),
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业资质许可表';

-- ============================================
-- 7. 知识产权表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_intellectual_property` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '知识产权 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `ip_type` TINYINT UNSIGNED NOT NULL COMMENT '类型：1=商标 2=专利 3=软著 4=版权',
  `ip_name` VARCHAR(200) NOT NULL COMMENT '名称',
  `registration_number` VARCHAR(100) COMMENT '注册/申请号',
  `status` VARCHAR(50) COMMENT '状态：申请中/已授权/驳回/无效',
  `application_date` DATE COMMENT '申请日期',
  `registration_date` DATE COMMENT '注册日期',
  `valid_to` DATE COMMENT '有效期至',
  `ip_class` VARCHAR(50) COMMENT '类别/分类号',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_company` (`company_id`),
  INDEX `idx_type` (`ip_type`),
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业知识产权表';

-- ============================================
-- 8. 分析报告表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_analysis_report` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '报告 ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `report_type` TINYINT UNSIGNED DEFAULT 1 COMMENT '报告类型：1=简易版 2=完整版 3=定制版',
  `report_title` VARCHAR(200) COMMENT '报告标题',
  `report_file` VARCHAR(255) COMMENT '报告文件路径',
  `risk_score` DECIMAL(5,2) COMMENT '风险评分',
  `risk_level` TINYINT UNSIGNED COMMENT '风险等级',
  `risk_count` INT UNSIGNED DEFAULT 0 COMMENT '风险数量统计',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '生成时间',
  `download_count` INT UNSIGNED DEFAULT 0 COMMENT '下载次数',
  INDEX `idx_user` (`user_id`),
  INDEX `idx_company` (`company_id`),
  FOREIGN KEY (`user_id`) REFERENCES `ers_user`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业风险分析报告表';

-- ============================================
-- 9. 数据采集记录表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_data_collection_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '记录 ID',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业 ID',
  `source_type` TINYINT UNSIGNED NOT NULL COMMENT '数据源类型：1=天眼查 2=国家公示系统 3=信用中国 4=裁判文书网 5=商标局 6=其他',
  `collection_type` TINYINT UNSIGNED DEFAULT 1 COMMENT '采集类型：1=手动 2=批量 3=定时',
  `status` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态：0=失败 1=成功',
  `data_count` INT UNSIGNED DEFAULT 0 COMMENT '采集数据条数',
  `error_message` TEXT COMMENT '错误信息',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '采集时间',
  INDEX `idx_company` (`company_id`),
  INDEX `idx_source` (`source_type`),
  INDEX `idx_created` (`created_at`),
  FOREIGN KEY (`company_id`) REFERENCES `ers_company`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='数据采集日志表';

-- ============================================
-- 10. 系统配置表
-- ============================================
CREATE TABLE IF NOT EXISTS `ers_system_config` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '配置 ID',
  `config_key` VARCHAR(100) NOT NULL UNIQUE COMMENT '配置键',
  `config_value` TEXT COMMENT '配置值',
  `config_type` VARCHAR(50) COMMENT '配置类型：string/json/boolean',
  `description` VARCHAR(255) COMMENT '配置说明',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  INDEX `idx_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- ============================================
-- 初始化系统配置
-- ============================================
INSERT INTO `ers_system_config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('app_name', '企业风险分析系统', 'string', '系统名称'),
('app_version', '1.0.0', 'string', '系统版本'),
('data_source_tianyancha_api_key', '', 'string', '天眼查 API Key'),
('data_source_tianyancha_enabled', '0', 'boolean', '天眼查数据源开关'),
('risk_score_threshold_low', '30', 'string', '低风险阈值'),
('risk_score_threshold_high', '70', 'string', '高风险阈值'),
('report_template_path', '/reports/templates/', 'string', '报告模板路径');
