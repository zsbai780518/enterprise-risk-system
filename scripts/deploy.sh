#!/bin/bash
# +----------------------------------------------------------------------
# | 企业风险分析系统 - 快速部署脚本
# +----------------------------------------------------------------------

set -e

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 配置
PROJECT_NAME="enterprise_risk_system"
DB_NAME="enterprise_risk_system"
DB_USER="root"
DB_PASS=""
PROJECT_DIR="/home/admin/openclaw/workspace/enterprise-risk-system"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  企业风险分析系统 V1.0 部署脚本${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# 检查 PHP 版本
echo -e "${YELLOW}[1/6] 检查 PHP 版本...${NC}"
PHP_VERSION=$(php -v | head -n 1 | cut -d' ' -f2)
echo "PHP 版本：$PHP_VERSION"
if ! php -v | grep -q "7.2.5\|7.3\|7.4\|8\."; then
    echo -e "${RED}错误：PHP 版本需要 >= 7.2.5${NC}"
    exit 1
fi
echo -e "${GREEN}✓ PHP 版本检查通过${NC}"
echo ""

# 检查 MySQL
echo -e "${YELLOW}[2/6] 检查 MySQL 连接...${NC}"
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}错误：未找到 MySQL 客户端${NC}"
    exit 1
fi

read -p "请输入 MySQL root 密码：" -s DB_PASS
echo ""

if ! mysql -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1" &> /dev/null; then
    echo -e "${RED}错误：无法连接 MySQL${NC}"
    exit 1
fi
echo -e "${GREEN}✓ MySQL 连接成功${NC}"
echo ""

# 创建数据库
echo -e "${YELLOW}[3/6] 创建数据库...${NC}"
mysql -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo -e "${GREEN}✓ 数据库创建成功${NC}"
echo ""

# 导入表结构
echo -e "${YELLOW}[4/6] 导入数据库表结构...${NC}"
mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$PROJECT_DIR/database/schema_v1.0.sql"
echo -e "${GREEN}✓ 表结构导入成功${NC}"
echo ""

# 配置后端
echo -e "${YELLOW}[5/6] 配置后端...${NC}"
cd "$PROJECT_DIR/backend"

# 更新数据库配置
sed -i "s/'password'        => ''/'password'        => '$DB_PASS'/" config/database.php
sed -i "s/'database'        => 'enterprise_risk_system'/'database'        => '$DB_NAME'/" config/database.php

# 设置目录权限
mkdir -p runtime/log
chmod -R 755 runtime/
chmod -R 755 public/

echo -e "${GREEN}✓ 后端配置完成${NC}"
echo ""

# 检查 Composer
echo -e "${YELLOW}[6/6] 检查 Composer 依赖...${NC}"
if command -v composer &> /dev/null; then
    if [ -f "composer.json" ]; then
        composer install --no-dev -o
        echo -e "${GREEN}✓ Composer 依赖安装完成${NC}"
    else
        echo -e "${YELLOW}⚠ 未找到 composer.json，跳过依赖安装${NC}"
    fi
else
    echo -e "${YELLOW}⚠ 未安装 Composer，请手动安装 ThinkPHP 依赖${NC}"
fi
echo ""

# 部署完成
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  部署完成！${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "后端地址：http://localhost/api/"
echo "前端项目：$PROJECT_DIR/frontend"
echo ""
echo -e "${YELLOW}下一步操作：${NC}"
echo "1. 配置 Apache/Nginx 虚拟主机，将 backend/public 设为网站根目录"
echo "2. 使用 HBuilderX 打开 frontend 目录，编译前端项目"
echo "3. 修改 frontend/api/index.js 中的 BASE_URL 为实际 API 地址"
echo ""
