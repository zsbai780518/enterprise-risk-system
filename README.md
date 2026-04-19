# Enterprise Risk Analysis System V1.0

[![License](https://img.shields.io/badge/license-proprietary-red.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.2.5+-blue.svg)](https://php.net)
[![ThinkPHP](https://img.shields.io/badge/ThinkPHP-6.x/8.x-green.svg)](https://thinkphp.cn)
[![UniAPP](https://img.shields.io/badge/UniAPP-Vue3-orange.svg)](https://uniapp.dcloud.net.cn)

企业全维度风险智能分析 SaaS 系统 - Enterprise-wide Risk Intelligent Analysis SaaS System

## 📖 简介 / Introduction

本系统是基于 **UniAPP（前端）+ ThinkPHP（后端）+ LAMP（服务器环境）** 架构开发的企业全维度风险智能分析 SaaS 系统。

**核心价值 / Core Values:**
- 📊 **数据整合** - 打通多平台企业公开数据壁垒
- 🤖 **智能分析** - AI 算法完成风险归类、等级评估
- 📈 **可视化呈现** - 股权穿透图、风险热力图、合规图谱
- 📋 **合规指导** - 针对性输出合规整改方案

## 🏗️ 技术架构 / Tech Stack

| 层级 Layer | 技术 Technology | 版本 Version |
|------------|-----------------|--------------|
| 服务器 Server | LAMP (Linux + Apache + MySQL + PHP) | CentOS 7+ / PHP 7.2.5+ |
| 后端 Backend | ThinkPHP | 6.x / 8.x |
| 前端 Frontend | UniAPP | Vue 3 |
| 数据库 Database | MySQL | 5.7+ |
| 可视化 Visualization | ECharts | 5.x |

## 📁 目录结构 / Directory Structure

```
enterprise-risk-system/
├── backend/                 # 后端 ThinkPHP 项目
│   ├── app/
│   │   ├── controller/     # 控制器
│   │   ├── model/          # 模型
│   │   └── service/        # 服务层
│   ├── config/             # 配置文件
│   ├── public/             # 入口文件
│   └── runtime/            # 运行时目录
├── frontend/               # 前端 UniAPP 项目
│   ├── pages/              # 页面
│   ├── components/         # 组件
│   ├── api/                # API 封装
│   └── static/             # 静态资源
├── database/               # 数据库脚本
│   └── schema_v1.0.sql    # V1.0 数据库设计
├── docs/                   # 项目文档
├── scripts/                # 部署/运维脚本
└── README.md              # 项目说明
```

## 🚀 快速开始 / Quick Start

### 1. 环境要求 / Requirements

```bash
# PHP 版本检查
php -v  # 需要 >= 7.2.5

# MySQL 版本检查
mysql -V  # 需要 >= 5.7

# Node.js 版本检查（前端开发）
node -v  # 需要 >= 14
```

### 2. 数据库初始化 / Database Setup

```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE enterprise_risk_system DEFAULT CHARACTER SET utf8mb4;"

# 导入表结构
mysql -u root -p enterprise_risk_system < database/schema_v1.0.sql
```

### 3. 后端配置 / Backend Configuration

编辑 `backend/config/database.php`:

```php
'hostname' => '127.0.0.1',
'database' => 'enterprise_risk_system',
'username' => 'root',
'password' => 'your_password',
```

### 4. 部署 / Deploy

```bash
# 使用一键部署脚本
cd enterprise-risk-system
bash scripts/deploy.sh
```

### 5. 前端开发 / Frontend Development

```bash
cd frontend

# 使用 HBuilderX 打开项目
# 或使用 CLI
npm install
npm run dev:mp-weixin  # 微信小程序
npm run dev:h5        # H5
```

## 📡 API 接口 / API Endpoints

### 用户相关 / User

| 接口 Endpoint | 方法 Method | 说明 Description |
|---------------|-------------|------------------|
| /api/user/register | POST | 用户注册 Register |
| /api/user/login | POST | 用户登录 Login |
| /api/user/logout | POST | 用户登出 Logout |
| /api/user/info | GET | 获取用户信息 Get user info |

### 企业相关 / Company

| 接口 Endpoint | 方法 Method | 说明 Description |
|---------------|-------------|------------------|
| /api/company/list | GET | 企业列表 Company list |
| /api/company/detail | GET | 企业详情 Company detail |
| /api/company/search | POST | 搜索企业 Search company |
| /api/company/collect | POST | 采集企业数据 Collect data |
| /api/company/bind | POST | 绑定企业 Bind company |

### 风险分析 / Risk Analysis

| 接口 Endpoint | 方法 Method | 说明 Description |
|---------------|-------------|------------------|
| /api/risk/analysis | GET | 风险分析 Risk analysis |
| /api/risk/list | GET | 风险列表 Risk list |
| /api/risk/statistics | GET | 风险统计 Risk statistics |

### 可视化数据 / Visualization

| 接口 Endpoint | 方法 Method | 说明 Description |
|---------------|-------------|------------------|
| /api/chart/equity | GET | 股权结构图 Equity chart |
| /api/chart/risk | GET | 风险地图 Risk map |
| /api/chart/qualification | GET | 资质合规图谱 Qualification chart |

## 📦 V1.0 功能范围 / V1.0 Features

- ✅ 用户注册/登录 User registration/login
- ✅ 企业数据手动采集（单企业）Manual company data collection
- ✅ 基础风险分析 Basic risk analysis
- ✅ 股权结构图生成 Equity structure chart
- ✅ 简易风险报告 Simple risk report
- ✅ 用户基础管理 Basic user management

## 🔌 数据源对接 / Data Sources (TODO)

| 数据源 Source | 类型 Type | 状态 Status |
|---------------|-----------|-------------|
| 天眼查 | API | 待对接 TODO |
| 国家企业信用信息公示系统 | 公开数据 Public | 待对接 TODO |
| 信用中国 | API | 待对接 TODO |
| 裁判文书网 | 公开数据 Public | 待对接 TODO |
| 商标局 | API | 待对接 TODO |

## 📝 开发计划 / Roadmap

- [x] Phase 1: 项目初始化与基础架构
- [ ] Phase 2: 用户管理模块开发
- [ ] Phase 3: 企业数据采集模块开发
- [ ] Phase 4: AI 智能分析模块开发
- [ ] Phase 5: 可视化生成模块开发
- [ ] Phase 6: 合规报告与路线规划模块
- [ ] Phase 7: 数据监控与预警模块
- [ ] Phase 8: 系统管理后台开发
- [ ] Phase 9: V1.0 版本集成与测试

## 📄 许可证 / License

 proprietary - 未经授权不得用于商业用途

## 👥 开发团队 / Team

- 创建时间 Created: 2026-04-20
- 架构 Architecture: UniAPP + ThinkPHP + LAMP

## 📞 联系方式 / Contact

如有问题请提交 Issue 或联系开发团队。

---

**企业风险分析系统** - 让企业风险管理更智能、更高效

**Enterprise Risk Analysis System** - Making enterprise risk management smarter and more efficient
