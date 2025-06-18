# SRSBSNS Test App

A comprehensive Symfony 7.3 application showcasing modern web development practices with API Platform, Bootstrap 5, user management, and advanced features.

**Created by: Lee van Rensburg**

## 🚀 Features

- ✅ **Contact System** - Advanced contact form with email notifications, CC support, and reCAPTCHA protection
- ✅ **User Management** - Complete user administration with role-based access control
- ✅ **REST API** - Full-featured API with JWT authentication and comprehensive documentation
- ✅ **Admin Panel** - Professional admin interface using NiceAdmin template with Bootstrap 5
- ✅ **Responsive Design** - Beautiful, modern UI with Bootstrap 5 and custom styling
- ✅ **Security** - JWT authentication, password hashing, and security best practices
- ✅ **Database Integration** - Doctrine ORM with MySQL support and production-ready schema
- ✅ **Email System** - SMTP integration with admin and optional CC recipients

## 🛠 Technology Stack

- **Framework**: Symfony 7.3
- **Frontend**: Bootstrap 5, NiceAdmin Template, Font Awesome Icons
- **Database**: MySQL 8.0+ with UTF8MB4 support
- **API**: API Platform 4.1 with OpenAPI documentation
- **Authentication**: Lexik JWT Bundle for API, Symfony Security for admin
- **Templates**: Twig with Bootstrap 5 form themes
- **PHP Version**: 8.2+

## 📋 System Requirements

- PHP 8.2 or higher
- Composer 2.0+
- MySQL 8.0 or higher
- Web server (Apache/Nginx) with mod_rewrite/try_files support
- OpenSSL extension for JWT key generation

## 🔧 Installation

### Method 1: Automated Setup (Recommended)

1. **Clone or extract the project**
   ```bash
   git clone <repository-url> srsbsns
   cd srsbsns
   ```

2. **Run the automated setup script**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

   This script will:
   - Install Composer dependencies
   - Generate JWT keys
   - Set up environment configuration
   - Create database structure
   - Create default users

### Method 2: Manual Installation

1. **Install dependencies**
   ```bash
   composer install
   ```

2. **Configure environment**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database credentials
   ```

3. **Generate JWT keys**
   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

4. **Set up database**
   ```bash
   # Import the production schema
   mysql -u username -p database_name < srsbsns.sql
   
   # OR create manually
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console app:create-users
   ```

5. **Start development server**
   ```bash
   symfony serve
   # OR
   php -S localhost:8000 -t public/
   ```

## 🗄 Database Configuration

### For Development/Testing
Update `.env.local` with your database credentials:
```env
# Database Configuration
DATABASE_URL="mysql://username:password@localhost:3306/database_name?serverVersion=8.0&charset=utf8mb4"

# Example provided configuration
DATABASE_URL="mysql://Admin1:KooKooLee975*#!@localhost:3306/srsbsns?serverVersion=8.0&charset=utf8mb4"
```

### Production Database Deployment
Use the provided `srsbsns.sql` file for production deployment:

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE srsbsns CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema and initial data
mysql -u username -p srsbsns < srsbsns.sql
```

The SQL file includes:
- Complete database schema with proper indexes
- Default admin and API users
- Initial configuration settings
- Sample data for testing
- Production deployment notes

## 👥 Default Access Credentials

### Admin Panel (`/admin/login`)
- **Email**: `admin@srsbsns.com`
- **Password**: `admin123`
- **Role**: ROLE_ADMIN

### API Access (`POST /api/login_check`)
- **Email**: `apiuser@srsbsns.com`
- **Password**: `api123` 
- **Role**: ROLE_API_USER

> ⚠️ **Security Warning**: Change these default credentials immediately in production!

## 📁 Project Structure

```
srsbsns/
├── config/                    # Application configuration
│   ├── packages/              # Bundle configurations
│   ├── routes/                # Routing configuration
│   └── jwt/                   # JWT public/private keys
├── migrations/                # Database migrations
├── NiceAdmin/                 # Admin template assets (source)
├── public/                    # Web root directory
│   ├── NiceAdmin/             # Admin template assets (public)
│   └── index.php              # Application entry point
├── src/
│   ├── Command/               # Console commands
│   ├── Controller/            # Controllers (Home, Admin)
│   ├── Entity/                # Doctrine entities
│   ├── Form/                  # Symfony forms
│   ├── Repository/            # Data repositories
│   └── Service/               # Business logic services
├── templates/                 # Twig templates
│   ├── admin/                 # Admin panel templates
│   ├── contact/               # Contact form templates
│   ├── home/                  # Public site templates
│   └── components/            # Reusable components
├── var/                       # Cache, logs, sessions
├── vendor/                    # Composer dependencies
├── srsbsns.sql               # Production database schema
└── setup.sh                  # Automated setup script
```

## ✨ Key Features

### 📧 Contact Management System

**Public Contact Form** (`/contact`)
- Responsive Bootstrap 5 form design
- Server-side validation with Symfony forms
- reCAPTCHA protection (configurable)
- Automatic email notifications to admin
- Optional CC email recipient support
- User confirmation emails

**Admin Contact Management** (`/admin/contacts`)
- Paginated contact listings with search
- Individual contact detail views
- Contact status management
- Export capabilities
- Email response tracking

### 👤 User Management System

**User Administration** (`/admin/users`)
- Create, edit, and delete users
- Role assignment (Admin, API User, Regular User)
- Account activation/deactivation
- Password management
- User activity tracking
- Secure password hashing with Symfony's password hasher

**Available User Roles**
- `ROLE_USER` - Basic user access
- `ROLE_ADMIN` - Administrative access to admin panel
- `ROLE_API_USER` - API access with JWT authentication

### 🔌 REST API

Built with API Platform, featuring:

**Authentication**
- JWT-based authentication
- Secure token generation and validation
- Role-based access control

**Endpoints**

| Method | Endpoint | Description | Auth Required | Role |
|--------|----------|-------------|---------------|------|
| POST | `/api/login_check` | Authenticate and get JWT token | No | - |
| GET | `/api/contacts` | List all contacts | Yes | API_USER |
| GET | `/api/contacts/{id}` | Get specific contact | Yes | API_USER |
| POST | `/api/contacts` | Create new contact | Yes | API_USER |

**API Documentation**
- Interactive Swagger UI at `/api`
- Custom documentation at `/api-docs`
- OpenAPI 3.0 specification

### 🎛 Admin Panel

Professional administration interface featuring:

**Dashboard** (`/admin`)
- System overview and statistics
- Recent contact submissions
- User activity monitoring
- Quick access to common tasks

**Settings Management** (`/admin/settings`)
- Admin email configuration
- Optional CC email recipient
- reCAPTCHA site and secret keys
- System configuration options

**API Setup** (`/admin/api-setup`)
- API documentation and testing interface
- JWT token management
- Endpoint testing tools

## ⚙️ Configuration

### Email Configuration

Configure SMTP settings in `.env.local`:

```env
# Email Configuration
MAILER_DSN="smtp://username:password@smtp.server.com:587"

# Alternative configurations
# Gmail: MAILER_DSN="gmail://username:password@default"
# SendGrid: MAILER_DSN="sendgrid://apikey@default"
# Local: MAILER_DSN="smtp://localhost:1025"
```

**Admin Email Settings**
Configure via admin panel at `/admin/settings`:
- **Admin Email**: Primary recipient for contact form notifications
- **CC Email**: Optional secondary recipient for contact notifications
- Leave CC Email blank to disable

### reCAPTCHA Setup

1. Register your domain at [Google reCAPTCHA](https://www.google.com/recaptcha/admin)
2. Configure keys via admin panel at `/admin/settings`:
   - **Site Key**: Client-side reCAPTCHA key
   - **Secret Key**: Server-side validation key

### JWT Configuration

JWT keys are automatically generated during setup. To regenerate:

```bash
php bin/console lexik:jwt:generate-keypair --overwrite
```

Keys are stored in `config/jwt/`:
- `private.pem` - Private key for token signing
- `public.pem` - Public key for token verification

## 🚀 Production Deployment

### 1. Prepare Environment

```bash
# Set production environment
APP_ENV=prod
APP_DEBUG=false

# Generate secure APP_SECRET
APP_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader --no-scripts
```

### 3. Deploy Database

```bash
# Import production schema
mysql -u username -p database_name < srsbsns.sql

# Verify database structure
php bin/console doctrine:schema:validate --env=prod
```

### 4. Configure Web Server

**Apache Configuration** (`apache-vhost-example.conf`)
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/srsbsns/public
    
    <Directory /var/www/html/srsbsns/public>
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/srsbsns_error.log
    CustomLog ${APACHE_LOG_DIR}/srsbsns_access.log combined
</VirtualHost>
```

**Nginx Configuration**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/srsbsns/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php-fpm;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
}
```

### 5. Set Permissions

```bash
# Set proper file permissions
chown -R www-data:www-data /var/www/html/srsbsns
chmod -R 755 /var/www/html/srsbsns
chmod -R 775 /var/www/html/srsbsns/var
```

### 6. Security Checklist

- [ ] Change default admin and API user passwords
- [ ] Configure HTTPS with SSL certificates
- [ ] Set up proper firewall rules
- [ ] Configure CORS settings for API access
- [ ] Enable opcache for PHP performance
- [ ] Set up regular database backups
- [ ] Configure log rotation
- [ ] Update all environment variables

## 🔒 Security Features

- **Password Security**: Bcrypt/Argon2 password hashing
- **JWT Authentication**: Secure API token-based authentication
- **CSRF Protection**: Built-in CSRF protection for forms
- **XSS Protection**: Twig template auto-escaping
- **SQL Injection Protection**: Doctrine ORM prepared statements
- **Role-Based Access**: Granular permissions system
- **Session Security**: Secure session configuration

## 🧪 Development Tools

### Console Commands

```bash
# Create users
php bin/console app:create-users

# Test user authentication
php bin/console app:test-auth email@example.com password

# Generate password hash
php bin/console security:hash-password

# Clear cache
php bin/console cache:clear

# Check routes
php bin/console debug:router

# Database operations
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Testing API Endpoints

```bash
# Get JWT token
curl -X POST http://localhost/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"email": "apiuser@srsbsns.com", "password": "api123"}'

# Use token to access protected endpoints
curl -X GET http://localhost/api/contacts \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## 🎨 UI/UX Features

- **Responsive Design**: Mobile-first Bootstrap 5 implementation
- **Modern Admin Interface**: NiceAdmin template with dark/light mode
- **Accessibility**: WCAG compliant form controls and navigation
- **Performance**: Optimized assets and minimal external dependencies
- **User Experience**: Intuitive navigation and clear feedback messages

## 📊 Database Schema

The application uses a well-structured database schema:

**Core Tables**
- `users` - User accounts with roles and authentication
- `contacts` - Contact form submissions with full details
- `admin_config` - Application configuration settings
- `doctrine_migration_versions` - Migration tracking

**Key Indexes**
- Email uniqueness on users table
- Contact date and email indexes for performance
- Configuration key uniqueness

## 🔄 Version Information

- **Application Version**: 1.0.0
- **Symfony Version**: 7.3
- **PHP Version**: 8.2+
- **Bootstrap Version**: 5.3
- **API Platform Version**: 4.1

## 🐛 Troubleshooting

### Common Issues

1. **500 Error on Admin Login**
   - Ensure Symfony Asset component is installed: `composer require symfony/asset`
   - Check file permissions on var/ directory

2. **API Authentication Fails**
   - Verify JWT keys exist in config/jwt/
   - Check user exists and passwords are correct
   - Ensure JWT bundle is properly configured

3. **Email Not Sending**
   - Verify MAILER_DSN configuration
   - Check SMTP server credentials
   - Test with development mailer (mail catcher)

4. **Database Connection Issues**
   - Verify database credentials in .env.local
   - Ensure MySQL server is running
   - Check character set and collation settings

## 📞 Support & Documentation

- **Symfony Documentation**: [https://symfony.com/doc/current/](https://symfony.com/doc/current/)
- **API Platform**: [https://api-platform.com/docs/](https://api-platform.com/docs/)
- **Bootstrap 5**: [https://getbootstrap.com/docs/5.3/](https://getbootstrap.com/docs/5.3/)
- **NiceAdmin Template**: Professional admin template integration

## 📝 License

This project is proprietary and created for demonstration purposes by Lee van Rensburg.

---

**SRSBSNS Test App** - A modern, full-featured Symfony application showcasing best practices in web development, API design, and user management.

*Built with ❤️ using Symfony 7.3, Bootstrap 5, and modern web technologies.* 