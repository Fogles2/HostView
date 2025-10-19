# HostView - PHP Hosting Billing Dashboard

![HostView](https://img.shields.io/badge/HostView-v1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

A comprehensive PHP hosting billing and client management system with FOSSBilling and Plesk integration, built with modern Tabler UI framework.

## 🚀 Features

### Admin Dashboard
- **Real-time Statistics**: Live data from FOSSBilling API
- **Client Management**: Complete client lifecycle with advanced filtering
- **Billing System**: Invoice generation, payment tracking, automated billing
- **Server Monitoring**: Real-time server resource monitoring via Plesk API
- **Modern UI**: Built with Tabler UI framework

### Client Portal
- **Service Overview**: Real-time service status and usage metrics
- **Billing Center**: Invoice management and payment processing
- **Support System**: Integrated ticket system
- **Account Management**: Profile settings and API key management

### Integrations
- **FOSSBilling**: Complete API integration for billing automation
- **Plesk**: Server management and domain operations
- **Payment Gateways**: Multiple payment processor support

## 🔧 Installation

### 1. Clone Repository
```bash
git clone https://github.com/Fogles2/HostView.git
cd HostView
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env with your FOSSBilling and database credentials
```

### 4. Database Setup
```bash
mysql -u root -p
CREATE DATABASE hostview;
USE hostview;
SOURCE database/schema.sql;
```

### 5. Set Permissions
```bash
chmod -R 755 .
chmod -R 777 storage/logs/
chmod -R 777 storage/cache/
```

## ⚙️ Configuration

### FOSSBilling Integration
```env
FOSSBILLING_URL=https://billing.turnpage.io
FOSSBILLING_API_KEY=your_api_key_here
FOSSBILLING_USERNAME=admin
```

### Database Configuration
```env
DB_HOST=localhost
DB_NAME=hostview
DB_USER=your_username
DB_PASS=your_password
```

## 🔐 Default Login Credentials

**Admin Access:**
- Username: `admin`
- Password: `Admin123!`
- URL: `https://yourdomain.com/admin`

**Client Access:**
- Username: `demo`
- Password: `Demo123!`
- URL: `https://yourdomain.com/client`

## 📖 Documentation

- [Installation Guide](docs/installation.md)
- [API Documentation](docs/api.md)
- [FOSSBilling Integration](docs/fossbilling-setup.md)
- [Plesk Integration](docs/plesk-setup.md)

## 🔒 Security

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention
- XSS protection
- Rate limiting
- Session security

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/Fogles2/HostView/issues)
- **Email**: support@turnpage.io
- **Website**: [https://turnpage.io](https://turnpage.io)

---

**Made with ❤️ by Turnpage Networks**