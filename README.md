<p align="center">
  <img src="/images/logo-baas.png" alt="在行云 BaaS 平台" width="200" />
</p>

# 在行云 BaaS 平台
View English documentation: [README_EN.md](README_EN.md)

## 项目介绍

[在行云](https://www.zaihangyun.com)是一款专为独立开发者打造的轻量级 BaaS（Backend as a Service）平台，帮助开发者无需自建后台、无需后端开发即可快速构建功能完善的 APP。平台提供成熟的系统模块和管理后台，涵盖用户管理、支付、文档、数据收集、版本管理等核心功能。

## 体验地址
线上功能与开源版功能一致，区别在于：
- **开源版**：免费，功能完善，支持二次开发。
- **线上版**：收费，免IDC成本，免部署，免运维，数据安全。
- **在线体验**：[https://www.zaihangyun.com](https://www.zaihangyun.com)

## 源码地址
- **国内**：[https://gitee.com/herenet/zaihangyun-CE](https://gitee.com/herenet/zaihangyun-CE)
- **国外**：[https://github.com/herenet/zaihangyun-CE](https://github.com/herenet/zaihangyun-CE)

## 项目特点

- **零后端开发门槛**：无需掌握后端开发技术，甚至无需写一行后端代码。
- **一键配置**：通过简单配置即可为你的 APP 自动生成功能齐全的管理后台。
- **主流平台集成**：已集成支付、退款、第三方登录等主流平台能力，省去繁琐对接工作。
- **免基础设施投入**：无需购买服务器、域名、带宽、HTTPS 证书等，真正实现「即开即用」。
- **运维与合规无忧**：无需担心服务器运维、数据备份、安全配置、系统监控及备案等问题。
- **灵活可迁移**：产品发展需要个性化功能时，可随时导出业务数据，自由迁移到私有后端。
- **极简 API 接入**：保证数据安全的前提下，简化 API 接入流程，让开发更高效。
- **模块化设计**：按需启用模块功能，避免冗余系统开销，管理后台更清爽、聚焦。
- **专为独立开发者优化**：从底层架构到 UI 交互均为个人开发者友好设计。
- **持续迭代**：欢迎开发者提出建议和需求，共同打磨产品。

## 功能模块

- **用户模块**：支持用户注册、登录和用户管理等基础功能，快速搭建用户体系。
- **售卖模块**：覆盖支付、退款、会员购买、功能开通、订单管理等 APP 常用变现核心能力。
- **文档模块**：帮助管理帮助文档、协议条款、自定义文档等内容，保障用户知情权与产品合规性。
- **数据收集模块**：收集用户反馈、常见问题（Q&A）、系统通知等信息，助力产品优化。
- **版本管理模块**：支持 APP 版本控制与更新策略，实现稳定迭代和高效发布。

## 技术架构

### 项目结构
- **app**： 项目主目录
   - **admin**：管理后台 - 基于laravel-admin框架构建
   - **api**：api接口 - 基于webman框架构建

### 特别说明
- **为确保API接口性能，同时保证后台配置能实时生效，admin与api共享一个Redis实例。**

## 接口文档

- **在线文档**：[https://www.zaihangyun.com/docs](https://www.zaihangyun.com/docs)

## 安装与部署

### 环境要求

- PHP >= 8.0
- MySQL >= 5.6
- Redis >= 5.0
- Composer

### Admin后台安装步骤

1. **克隆仓库**
   ```bash
   git clone https://gitee.com/herenet/zaihangyun-CE.git
   cd zaihangyun-CE/admin
   ```

2. **安装依赖**
   ```bash
   composer install
   ```

3. **配置环境**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **数据库迁移**
   ```bash
   mysql -u 用户名 -p 数据库名 < /zaihangyun-admin/zaihangyun-admin.sql
   ```

5. **启动服务**
   ```bash
   php artisan serve
   ```

6. **访问后台**
   打开浏览器，访问 `http://localhost:8000/admin` 即可进入后台管理界面。默认账号密码为 `admin` / `admin`。
   登录后，你可以在左侧导航栏中找到不同的模块，如用户管理、支付管理、文档管理等。

### API接口安装步骤

1. **选择目录**
   ```bash
   cd zaihangyun-CE/api
   ```

2. **安装依赖**
   ```bash
   composer install
   ```

3. **配置环境**
   ```bash
   cp .env.example .env
   ```

4. **启动服务**
   ```bash
   php start.php start
   ```

5. **访问接口**
   访问 `http://localhost:8787` 调用API接口

## 使用说明

- **用户管理**：通过后台管理界面管理用户注册、登录、权限分配等。
- **支付管理**：集成支付宝、微信支付、Apple IAP 等主流支付方式，支持订单管理、退款处理等。
- **文档管理**：通过文档模块管理帮助文档、协议条款、自定义文档等内容。
- **数据收集**：收集用户反馈、常见问题（Q&A）、系统通知等信息。
- **版本管理**：支持 APP 版本控制与更新策略，实现稳定迭代和高效发布。

## 贡献指南

欢迎开发者参与共建！你可以通过以下方式贡献：
- 提交 Issue：报告 Bug 或提出功能建议。
- 提 Pull Request：修复 Bug 或实现新功能。
- 参与讨论：通过评论或提出建议帮助优化产品。

## 协议

本项目采用 [Apache License 2.0](LICENSE)，你可以自由使用、修改和分发本项目代码。

## 联系我们

扫码添加官方微信，第一时间了解产品动态并参与共建。

![微信二维码](admin/public/images/wechat.jpg)