#!/bin/bash

# AnsiblePHP Quick Setup Script
# This script automates the initial setup process

echo "🚀 AnsiblePHP Quick Setup"
echo "========================="
echo ""

# Check if .env exists
if [ -f .env ]; then
    echo "⚠️  .env file already exists. Skipping environment setup."
else
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created"
fi

# Check if APP_KEY is set
if grep -q "APP_KEY=$" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated"
else
    echo "✅ Application key already exists"
fi

# Install composer dependencies
if [ -d "vendor" ]; then
    echo "✅ Composer dependencies already installed"
else
    echo "📦 Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo "✅ Composer dependencies installed"
fi

# Install npm dependencies
if [ -d "node_modules" ]; then
    echo "✅ NPM dependencies already installed"
else
    echo "📦 Installing NPM dependencies..."
    npm install
    echo "✅ NPM dependencies installed"
fi

echo ""
echo "⚡ Next Steps:"
echo "=============="
echo ""
echo "1. Configure your database in .env file:"
echo "   DB_CONNECTION=pgsql"
echo "   DB_HOST=127.0.0.1"
echo "   DB_PORT=5432"
echo "   DB_DATABASE=ansiblephp"
echo "   DB_USERNAME=postgres"
echo "   DB_PASSWORD=your_password"
echo ""
echo "2. Ensure Redis is running for Laravel Horizon"
echo ""
echo "3. Run migrations:"
echo "   php artisan migrate"
echo ""
echo "4. (Optional) Seed demo data:"
echo "   php artisan db:seed --class=DemoDataSeeder"
echo ""
echo "5. Create admin user:"
echo "   php artisan make:filament-user"
echo ""
echo "6. Build frontend assets:"
echo "   npm run build"
echo ""
echo "7. Start Laravel Horizon (in separate terminal):"
echo "   php artisan horizon"
echo ""
echo "8. Start development server:"
echo "   php artisan serve"
echo ""
echo "9. Access admin panel at: http://localhost:8000/admin"
echo ""
echo "✨ Setup preparation complete!"
