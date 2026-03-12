FROM dunglas/frankenphp:php8.4

WORKDIR /app

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js + npm
RUN apt-get update && apt-get install -y nodejs npm

# Install PHP extensions
RUN install-php-extensions pdo_mysql gd redis zip

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
RUN npm install

# Build Vite assets
RUN npm run build

# Clear config first
RUN php artisan config:clear

# Optimize Laravel (cache config, routes, views)
RUN php artisan optimize

# Storage symlink for uploaded files
RUN php artisan storage:link

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
