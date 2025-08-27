# 🚀 HƯỚNG DẪN TRIỂN KHAI HỆ THỐNG BỐC SỐ THỨ TỰ

## 📋 Yêu cầu hệ thống

### Server Requirements
- **PHP**: 8.1+ với các extension: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD, Fileinfo
- **Database**: MySQL 8.0+ hoặc MariaDB 10.5+
- **Web Server**: Nginx hoặc Apache
- **Memory**: Tối thiểu 2GB RAM
- **Storage**: Tối thiểu 10GB (không bao gồm file upload)

### Software Requirements
- **Composer**: 2.0+
- **Node.js**: 16.0+ (cho build frontend)
- **Redis**: 6.0+ (khuyến nghị cho cache và queue)

## 🔧 Cài đặt và cấu hình

### 1. Clone và cài đặt dependencies
```bash
git clone <repository-url>
cd bocso-hccv
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 2. Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

**Cập nhật file .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hdev_admin
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

QUEUE_CONNECTION=database
CACHE_DRIVER=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cấu hình file upload
UPLOAD_MAX_FILESIZE=128M
POST_MAX_SIZE=128M
MAX_EXECUTION_TIME=300

# Cấu hình mail (nếu cần)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### 3. Cấu hình database
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 4. Cấu hình storage và cache
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Cấu hình queue worker
```bash
# Tạo service cho queue worker
sudo nano /etc/systemd/system/laravel-queue.service
```

**Nội dung service file:**
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=append:/path/to/your/project/storage/logs/queue.log
StandardError=append:/path/to/your/project/storage/logs/queue.log

[Install]
WantedBy=multi-user.target
```

**Kích hoạt service:**
```bash
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
sudo systemctl status laravel-queue
```

### 6. Cấu hình Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    
    root /path/to/your/project/public;
    index index.php index.html;
    
    # Bảo mật
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;
    
    # File upload size
    client_max_body_size 128M;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|doc|docx)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 7. Cấu hình PHP-FPM
```ini
; /etc/php/8.1/fpm/php.ini
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

## 🔒 Bảo mật

### 1. File permissions
```bash
sudo chown -R www-data:www-data /path/to/your/project
sudo chmod -R 755 /path/to/your/project
sudo chmod -R 775 /path/to/your/project/storage
sudo chmod -R 775 /path/to/your/project/bootstrap/cache
```

### 2. Firewall
```bash
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### 3. SSL Certificate
```bash
# Sử dụng Let's Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## 📊 Monitoring và Logging

### 1. Log rotation
```bash
sudo nano /etc/logrotate.d/laravel
```

**Nội dung:**
```
/path/to/your/project/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.1-fpm
    endscript
}
```

### 2. Health check
```bash
# Tạo cron job để kiểm tra queue worker
crontab -e
```

**Thêm vào crontab:**
```bash
* * * * * cd /path/to/your/project && php artisan queue:work --once --timeout=60
0 2 * * * cd /path/to/your/project && php artisan queue:restart
```

## 🚀 Deployment

### 1. Automated deployment script
```bash
#!/bin/bash
# deploy.sh

cd /path/to/your/project

# Pull latest changes
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Build frontend
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue worker
sudo systemctl restart laravel-queue

# Reload nginx
sudo systemctl reload nginx

echo "Deployment completed successfully!"
```

### 2. Make script executable
```bash
chmod +x deploy.sh
```

## 📈 Performance Optimization

### 1. Redis configuration
```bash
sudo nano /etc/redis/redis.conf
```

**Cập nhật:**
```conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### 2. OPcache configuration
```ini
; /etc/php/8.1/fpm/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

## 🔍 Troubleshooting

### 1. Kiểm tra logs
```bash
tail -f /path/to/your/project/storage/logs/laravel.log
tail -f /path/to/your/project/storage/logs/queue.log
```

### 2. Kiểm tra queue status
```bash
php artisan queue:work --once
php artisan queue:failed
```

### 3. Kiểm tra permissions
```bash
ls -la /path/to/your/project/storage
ls -la /path/to/your/project/bootstrap/cache
```

## 📞 Support

Nếu gặp vấn đề, vui lòng kiểm tra:
1. Laravel logs trong `storage/logs/`
2. Nginx error logs
3. PHP-FPM error logs
4. Queue worker status

**Liên hệ hỗ trợ:**
- Email: support@your-domain.com
- Documentation: https://your-domain.com/docs
