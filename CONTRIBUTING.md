# 贡献指南 / Contributing

欢迎贡献企业风险分析系统！

## 🤝 如何贡献

### 1. Fork 仓库

点击 GitHub 页面右上角的 **Fork** 按钮

### 2. 克隆仓库

```bash
git clone https://github.com/你的用户名/enterprise-risk-system.git
cd enterprise-risk-system
```

### 3. 创建分支

```bash
git checkout -b feature/your-feature-name
```

### 4. 提交代码

```bash
git add .
git commit -m "feat: 添加 xxx 功能"
git push origin feature/your-feature-name
```

### 5. 创建 Pull Request

在 GitHub 上提交 PR，描述你的改动

---

## 📝 提交规范

### Commit Message 格式

```
<type>(<scope>): <subject>
```

### Type 类型

- `feat`: 新功能
- `fix`: 修复 bug
- `docs`: 文档更新
- `style`: 代码格式调整
- `refactor`: 重构
- `test`: 测试相关
- `chore`: 构建/工具链相关

### 示例

```
feat(user): 添加用户注册功能
fix(api): 修复登录接口 token 验证问题
docs(readme): 更新安装说明
```

---

## 🏗️ 开发环境搭建

### 后端

```bash
cd backend
composer install
cp config/database.local.php.example config/database.local.php
# 编辑数据库配置
```

### 前端

```bash
cd frontend
# 使用 HBuilderX 打开项目
# 或安装依赖
npm install
```

---

## 📦 发布流程

1. 更新 `manifest.json` 中的版本号
2. 更新 `README.md` 中的变更日志
3. 创建 Git Tag
4. 发布 GitHub Release

---

## 🙏 感谢所有贡献者

感谢每一位为这个项目做出贡献的开发者！
