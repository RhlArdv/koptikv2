FROM dunglas/frankenphp:php8.4

WORKDIR /app

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js + npm
RUN apt-get update && apt-get install -y nodejs npm

# Install PHP extensions
RUN install-php-extensions pdo_mysql gd redis zip

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
RUN npm install

# Build Vite assets
RUN npm run build

# Clear & rebuild Laravel config cache
RUN php artisan config:clear
RUN php artisan config:cache

# Create storage symlink
RUN php artisan storage:link

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
