#!/bin/bash

echo "🚀 SRSBSNS Test App Setup Script"
echo "================================"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

echo "✅ Prerequisites check passed"

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Create directories
echo "📁 Creating required directories..."
mkdir -p var/cache var/log config/jwt

# Set permissions
echo "🔧 Setting permissions..."
chmod 755 var/cache var/log
chmod 700 config/jwt

# Generate JWT keys if they don't exist
if [ ! -f "config/jwt/private.pem" ]; then
    echo "🔑 Generating JWT keys..."
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkcs8 -aes256 -pass pass:srsbsns123
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:srsbsns123
    chmod 600 config/jwt/private.pem config/jwt/public.pem
else
    echo "✅ JWT keys already exist"
fi

# Check if .env.local exists
if [ ! -f ".env.local" ]; then
    echo "⚙️  Creating .env.local file..."
    cat > .env.local << 'EOF'
# Local environment configuration
APP_ENV=prod
APP_SECRET=change-this-in-production

# Database configuration
# Replace with your actual database credentials
DATABASE_URL="mysql://username:password@localhost:3306/database_name?serverVersion=8.0&charset=utf8mb4"

# Mailer configuration
MAILER_DSN=smtp://localhost

# JWT configuration
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=srsbsns123

# Application settings
CONTACT_ADMIN_EMAIL=admin@example.com
RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
EOF
    echo "📝 Please edit .env.local with your database credentials"
else
    echo "✅ .env.local already exists"
fi

# Clear cache
echo "🧹 Clearing cache..."
php bin/console cache:clear --env=prod --no-debug

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "Next steps:"
echo "1. Edit .env.local with your database credentials"
echo "2. Run: php bin/console doctrine:database:create"
echo "3. Run: php bin/console doctrine:migrations:migrate"
echo "4. Configure your web server to point to the 'public/' directory"
echo ""
echo "Default admin credentials:"
echo "Username: admin"
echo "Password: admin123"
echo ""
echo "Default API credentials:"
echo "Username: apiuser" 
echo "Password: api123"
echo ""
echo "Happy coding! 🚀" 