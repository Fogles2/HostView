# HostView Installation Guide

Complete installation instructions for HostView PHP hosting billing dashboard.

## System Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 8.0+ or MariaDB 10.4+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Extensions**: PDO, cURL, OpenSSL, JSON, XML, ZIP, GD, mbstring

## Installation Steps

### 1. Clone Repository
```bash
git clone https://github.com/Fogles2/HostView.git
cd HostView
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env file with your settings
```

### 4. Database Setup
```sql
CREATE DATABASE hostview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hostview_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON hostview.* TO 'hostview_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Web Server Configuration

#### Apache
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/hostview/public
    
    <Directory /path/to/hostview/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/hostview/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Set Permissions
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 777 storage/
```

### 7. FOSSBilling Configuration

Update your `.env` file:
```env
FOSSBILLING_URL=https://billing.turnpage.io
FOSSBILLING_API_KEY=trGUFHOHLOt19wqDLEkhORT4iD8JNTuR
FOSSBILLING_USERNAME=admin
```

## Default Credentials

**Admin Login:**
- Username: `admin`
- Password: `Admin123!`

**Demo Client:**
- Username: `demo`
- Password: `Demo123!`

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /path/to/hostview
   sudo chmod -R 777 storage/
   ```

2. **FOSSBilling Connection Issues**
   - Verify API key in FOSSBilling admin panel
   - Check firewall settings
   - Test connection: `curl -X POST https://billing.turnpage.io/api/admin/staff/profile`

3. **Composer Issues**
   ```bash
   composer clear-cache
   composer install --no-cache
   ```

For more help, check the [GitHub Issues](https://github.com/Fogles2/HostView/issues).