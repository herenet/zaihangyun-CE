<p align="left">
  <img src="admin/public/images/logo-mini.png" alt="Zaihangyun BaaS Platform" width="160" />
</p>
<p align="left">
  <img src="https://img.shields.io/badge/CE-master-blue.svg">
  <img src="https://img.shields.io/badge/License-Apache2.0-lightgrey.svg">
  <img src="https://img.shields.io/badge/PHP-8.0+-blue.svg">
  <img src="https://img.shields.io/badge/MySQL-5.7+-blue.svg">
  <img src="https://img.shields.io/badge/Redis-5.0+-blue.svg">
</p>

# Zaihangyun BaaS Platform
查看中文版本：[README.md](README.md)

## Project Introduction

[Zaihangyun](https://www.zaihangyun.com) is a lightweight BaaS (Backend as a Service) platform specifically designed for independent developers, helping them quickly build fully functional apps without setting up their own backend or requiring backend development. The platform provides mature system modules and an admin dashboard, covering core functionalities such as user management, payment, documentation, data collection, version management, and more.

## Experience Address
The online version has the same functionality as the open-source version, with the following differences:
- **Open-source Version**: Free, fully functional, supports secondary development.
- **Online Version**: Paid service, no IDC costs, no deployment needed, no maintenance required, data security guaranteed.
- **Online Experience**: [https://www.zaihangyun.com](https://www.zaihangyun.com)

## Project Features

- **Zero Backend Development Threshold**: No need to master backend development technologies, or even write a single line of backend code.
- **One-click Configuration**: Generate a fully functional admin dashboard for your APP through simple configuration.
- **Mainstream Platform Integration**: Already integrated with payment, refund, third-party login and other mainstream platform capabilities, eliminating tedious integration work.
- **No Infrastructure Investment**: No need to purchase servers, domain names, bandwidth, HTTPS certificates, etc., truly achieving "plug and play".
- **Worry-free Maintenance and Compliance**: No need to worry about server maintenance, data backup, security configuration, system monitoring, and filing requirements.
- **Flexible and Migratable**: When product development requires personalized features, you can export business data at any time and freely migrate to a private backend.
- **Minimalist API Integration**: Simplify the API integration process while ensuring data security, making development more efficient.
- **Modular Design**: Enable module functionality as needed, avoid redundant system overhead, making the admin dashboard cleaner and more focused.
- **Optimized for Independent Developers**: From underlying architecture to UI interaction, all designed to be friendly for individual developers.
- **Continuous Iteration**: Developers are welcome to provide suggestions and requirements to refine the product together.

## Functional Modules

- **User Module**: Supports basic functions such as user registration, login, and user management, quickly building a user system.
- **Sales Module**: Covers payment, refunds, membership purchases, feature activation, order management, and other core monetization capabilities commonly used in APPs.
- **Documentation Module**: Helps manage help documents, terms and agreements, custom documents, and other content, ensuring user informed consent and product compliance.
- **Data Collection Module**: Collects user feedback, frequently asked questions (Q&A), system notifications, and other information to help optimize the product.
- **Version Management Module**: Supports APP version control and update strategies, enabling stable iteration and efficient release.

## Technical Architecture

### Project Structure
- **app**: Main project directory
   - **admin**: Admin dashboard - Built on the laravel-admin framework
   - **api**: API interface - Built on the webman framework

### Special Note
- **To ensure API interface performance while ensuring admin configurations take effect in real-time, admin and api share a Redis instance.**

## API Documentation

- **Online Documentation**: [https://www.zaihangyun.com/docs](https://www.zaihangyun.com/docs)

## Installation and Deployment

### Environment Requirements

- PHP >= 8.0
- MySQL >= 5.7
- Redis >= 5.0
- Composer

### Admin Dashboard Installation Steps

1. **Clone Repository**
   ```bash
   git clone https://github.com/herenet/zaihangyun-CE.git
   cd zaihangyun-CE/admin
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration**
   ```bash
   mysql -u 用户名 -p 数据库名 < /zaihangyun-admin/zaihangyun-admin.sql
   ```

5. **Start Service**
   ```bash
   php artisan serve
   ```

6. **Access Admin Dashboard**
   Open your browser and visit `http://localhost:8000/admin` to enter the admin dashboard. The default account password is `admin` / `admin`.
   After logging in, you can find different modules in the left navigation bar, such as user management, payment management, documentation management, etc.

### Online Admin Dashboard Nginx Configuration
```bash
   server {
      listen 443 ssl;
      listen [::]:443 ssl;

      ssl_certificate /etc/nginx/cert/www.domain.com.pem;
      ssl_certificate_key  /etc/nginx/cert/www.domain.com.key;
      ssl_session_timeout 5m;
      ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
      ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
      ssl_prefer_server_ciphers on;

      root /path/zaihangyun-CE/admin/public;
      index index.html index.htm index.php;

      server_name www.domain.com domain.com;

      location / {
               try_files $uri $uri/ /index.php?$query_string;
      }

      location ~ \.php$ {
         include snippets/fastcgi-php.conf;
         fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
      }

      location ~ \.phtml$ {
         deny all;
      }

      location ~ /\.ht {
         deny all;
      }
   }
```

### API Interface Installation Steps

1. **Select Directory**
   ```bash
   cd zaihangyun-CE/api
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   ```

4. **Create Symbolic Links**
   - To ensure the API platform can correctly access certificates configured in the admin backend, configure the following symbolic links:
   ```bash
   cd api/storage/app
   ln -s ../../admin/storage/app/public/mch ./
   ```

   - For avatars and image-related files:
   ```bash
   cd api/public/
   ln -s ../storage ./
   ```

5. **Start Service**
   ```bash
   php start.php start
   ```

6. **Access API**
   Visit `http://localhost:8787` to call the API interface

### Online API Interface Nginx Configuration
   ```bash
   upstream webman {
      server 127.0.0.1:8787;
      keepalive 10240;
   }

   server {
      server_name api.domain.com;
      listen 443 ssl;
      listen [::]:443 ssl;
      ssl_certificate /etc/nginx/cert/api.domain.com.pem;
      ssl_certificate_key  /etc/nginx/cert/api.domain.com.key;
      ssl_session_timeout 5m;
      ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
      ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
      ssl_prefer_server_ciphers on;

      access_log off;
      # 注意，这里一定是webman下的public目录，不能是webman根目录
      root /path/zaihangyun-CE/api/public;

      location ^~ / {
         # 动态设置 CORS Origin，允许所有 *.domain.com 域名
         set $cors_origin "";
         if ($http_origin ~* "^https?://(.*\.)?domain\.com$") {
            set $cors_origin $http_origin;
         }

         # 添加 CORS 头
         add_header 'Access-Control-Allow-Origin' $cors_origin always;
         add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
         add_header 'Access-Control-Allow-Headers' 'Content-Type, Authorization, X-Requested-With, Accept, Origin' always;
         add_header 'Access-Control-Allow-Credentials' 'true' always;

         # 处理 OPTIONS 预检请求
         if ($request_method = 'OPTIONS') {
            add_header 'Access-Control-Allow-Origin' $cors_origin;
            add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS';
            add_header 'Access-Control-Allow-Headers' 'Content-Type, Authorization, X-Requested-With, Accept, Origin';
            add_header 'Access-Control-Allow-Credentials' 'true';
            add_header 'Access-Control-Max-Age' 1728000;
            add_header 'Content-Type' 'text/plain; charset=utf-8';
            add_header 'Content-Length' 0;
            return 204;
         }

         proxy_set_header Host $http_host;
         proxy_set_header X-Forwarded-For $remote_addr;
         proxy_set_header X-Forwarded-Proto $scheme;
         proxy_set_header X-Real-IP $remote_addr;
         proxy_http_version 1.1;
         proxy_set_header Connection "";
         if (!-f $request_filename){
            proxy_pass http://webman;
         }
      }

      # 拒绝访问所有以 .php 结尾的文件
      location ~ \.php$ {
            return 404;
      }

      # 允许访问 .well-known 目录
      location ~ ^/\.well-known/ {
            allow all;
      }

      # 拒绝访问所有以 . 开头的文件或目录
      location ~ /\. {
            return 404;
      }
   }
   ```

## Usage Instructions
- User Management : Manage user registration, login, permission assignment, etc. through the admin interface.
- Payment Management : Integrate mainstream payment methods such as Alipay, WeChat Pay, Apple IAP, etc., supporting order management, refund processing, etc.
- Document Management : Manage help documents, terms and agreements, custom documents, and other content through the document module.
- Data Collection : Collect user feedback, frequently asked questions (Q&A), system notifications, and other information.
- Version Management : Support APP version control and update strategies, enabling stable iteration and efficient release.
## Contribution Guidelines
Developers are welcome to participate in co-building! You can contribute in the following ways:

- Submit Issues: Report bugs or suggest features.
- Submit Pull Requests: Fix bugs or implement new features.
- Participate in Discussions: Help optimize the product through comments or suggestions.
## License
This project is licensed under the Apache License 2.0 , you are free to use, modify, and distribute the code of this project.

## Contact Us
Scan the QR code to add our official WeChat account and be the first to learn about product updates and participate in co-building.
![WeChat QR Code](admin/public/images/wechat.jpg)