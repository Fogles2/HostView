#!/bin/bash

# HostView Setup Script
# Automated installation script for HostView PHP Hosting Dashboard

set -e

echo "🚀 HostView Setup Script"
echo "======================================"
echo "Setting up HostView with FOSSBilling integration..."
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}❌ Please do not run this script as root${NC}"
    exit 1
fi

# Check PHP version
echo -e "${BLUE}📋 Checking PHP version...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP is not installed${NC}"
    echo "Please install PHP 8.2 or higher"
    exit 1
fi

PHP_VERSION=$(php -v | head -n1 | cut -d" " -f2 | cut -f1-2 -d".")
if [ "$(printf '%s\n' "8.2" "$PHP_VERSION" | sort -V | head -n1)" = "8.2" ]; then
    echo -e "${GREEN}✅ PHP $PHP_VERSION detected${NC}"
else
    echo -e "${RED}❌ PHP 8.2+ required, found $PHP_VERSION${NC}"
    exit 1
fi

# Check required PHP extensions
echo -e "${BLUE}🔧 Checking PHP extensions...${NC}"
REQUIRED_EXTENSIONS=("curl" "json" "pdo" "pdo_mysql" "openssl" "mbstring")
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "$ext"; then
        echo -e "${GREEN}✅ $ext${NC}"
    else
        echo -e "${RED}❌ $ext missing${NC}"
        MISSING_EXTENSIONS=true
    fi
done

if [ "$MISSING_EXTENSIONS" = true ]; then
    echo -e "${RED}Please install missing PHP extensions${NC}"
    exit 1
fi

# Check Composer
echo -e "${BLUE}📦 Checking Composer...${NC}"
if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}⚠️  Composer not found, installing...${NC}"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --quiet
    rm composer-setup.php
    sudo mv composer.phar /usr/local/bin/composer
    echo -e "${GREEN}✅ Composer installed${NC}"
else
    echo -e "${GREEN}✅ Composer found${NC}"
fi

# Install dependencies
echo -e "${BLUE}📥 Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader --quiet
echo -e "${GREEN}✅ Dependencies installed${NC}"

# Setup environment file
echo -e "${BLUE}⚙️  Setting up environment...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ Environment file created${NC}"
else
    echo -e "${YELLOW}⚠️  Environment file already exists${NC}"
fi

# Create directories
echo -e "${BLUE}📁 Creating directories...${NC}"
mkdir -p storage/logs storage/cache
echo -e "${GREEN}✅ Directories created${NC}"

# Set permissions
echo -e "${BLUE}🔐 Setting permissions...${NC}"
chmod -R 755 .
chmod -R 777 storage/
echo -e "${GREEN}✅ Permissions set${NC}"

# Database setup
echo ""
echo -e "${BLUE}🗄️  Database Configuration${NC}"
echo "======================================"
read -p "Database host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database name [hostview]: " DB_NAME
DB_NAME=${DB_NAME:-hostview}

read -p "Database username: " DB_USER
read -s -p "Database password: " DB_PASS
echo ""

# Update .env file
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

echo -e "${GREEN}✅ Database configuration updated${NC}"

# FOSSBilling configuration
echo ""
echo -e "${BLUE}🔗 FOSSBilling Configuration${NC}"
echo "======================================"
echo "Default URL: https://billing.turnpage.io"
read -p "FOSSBilling URL [https://billing.turnpage.io]: " FOSS_URL
FOSS_URL=${FOSS_URL:-https://billing.turnpage.io}

echo "Default API Key: trGUFHOHLOt19wqDLEkhORT4iD8JNTuR"
read -p "FOSSBilling API Key [trGUFHOHLOt19wqDLEkhORT4iD8JNTuR]: " FOSS_KEY
FOSS_KEY=${FOSS_KEY:-trGUFHOHLOt19wqDLEkhORT4iD8JNTuR}

read -p "FOSSBilling Admin Username [admin]: " FOSS_USER
FOSS_USER=${FOSS_USER:-admin}

# Update .env file
sed -i "s|FOSSBILLING_URL=.*|FOSSBILLING_URL=$FOSS_URL|" .env
sed -i "s/FOSSBILLING_API_KEY=.*/FOSSBILLING_API_KEY=$FOSS_KEY/" .env
sed -i "s/FOSSBILLING_USERNAME=.*/FOSSBILLING_USERNAME=$FOSS_USER/" .env

echo -e "${GREEN}✅ FOSSBilling configuration updated${NC}"

# Test FOSSBilling connection
echo -e "${BLUE}🧪 Testing FOSSBilling connection...${NC}"
TEST_RESULT=$(curl -s -X POST "$FOSS_URL/api/admin/staff/profile" \
    -H "Authorization: Basic $(echo -n "$FOSS_USER:$FOSS_KEY" | base64)" \
    -H "Content-Type: application/json" \
    --connect-timeout 10 || echo "FAILED")

if [[ $TEST_RESULT == *"FAILED"* ]] || [[ $TEST_RESULT == *"error"* ]]; then
    echo -e "${YELLOW}⚠️  FOSSBilling connection test failed${NC}"
    echo "You may need to check your API credentials later"
else
    echo -e "${GREEN}✅ FOSSBilling connection successful${NC}"
fi

# Generate application key
echo -e "${BLUE}🔑 Generating application key...${NC}"
APP_KEY=$(openssl rand -hex 16)
sed -i "s/APP_KEY=.*/APP_KEY=$APP_KEY/" .env
echo -e "${GREEN}✅ Application key generated${NC}"

# Web server configuration
echo ""
echo -e "${BLUE}🌐 Web Server Setup${NC}"
echo "======================================"
echo "Please configure your web server to point to the 'public' directory."
echo ""
echo "Apache Virtual Host example:"
echo "<VirtualHost *:80>"
echo "    ServerName yourdomain.com"
echo "    DocumentRoot $(pwd)/public"
echo "    <Directory $(pwd)/public>"
echo "        AllowOverride All"
echo "        Require all granted"
echo "    </Directory>"
echo "</VirtualHost>"
echo ""
echo "Nginx configuration example:"
echo "server {"
echo "    listen 80;"
echo "    server_name yourdomain.com;"
echo "    root $(pwd)/public;"
echo "    index index.php;"
echo ""
echo "    location / {"
echo "        try_files \$uri \$uri/ /index.php?\$query_string;"
echo "    }"
echo ""
echo "    location ~ \\.php\$ {"
echo "        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;"
echo "        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;"
echo "        include fastcgi_params;"
echo "    }"
echo "}"
echo ""

# Setup complete
echo -e "${GREEN}🎉 Setup Complete!${NC}"
echo "======================================"
echo ""
echo "HostView has been successfully set up!"
echo ""
echo -e "${BLUE}📋 Next Steps:${NC}"
echo "1. Configure your web server to point to the 'public' directory"
echo "2. Create the database: $DB_NAME"
echo "3. Access your admin panel at: http://yourdomain.com/admin"
echo ""
echo -e "${BLUE}🔐 Default Login Credentials:${NC}"
echo "Admin - Username: admin, Password: Admin123!"
echo "Demo Client - Username: demo, Password: Demo123!"
echo ""
echo -e "${BLUE}📊 Features:${NC}"
echo "• Real-time FOSSBilling integration"
echo "• Modern Tabler UI dashboard"
echo "• Client and invoice management"
echo "• Server monitoring (Plesk integration ready)"
echo "• RESTful API endpoints"
echo ""
echo -e "${BLUE}📖 Documentation:${NC}"
echo "• Installation Guide: docs/installation.md"
echo "• API Documentation: docs/api.md"
echo "• FOSSBilling Setup: docs/fossbilling-setup.md"
echo ""
echo -e "${BLUE}🆘 Support:${NC}"
echo "• GitHub: https://github.com/Fogles2/HostView"
echo "• Email: support@turnpage.io"
echo ""
echo "Thank you for using HostView! 🚀"