FROM dunglas/frankenphp:php8.4

WORKDIR /app

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install node + npm
RUN apt-get update && apt-get install -y nodejs npm

# Install PHP extensions
RUN install-php-extensions pdo_mysql gd redis zip

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install node dependencies
RUN npm install

# Build Vite
RUN npm run build

# Clear config only (AMAN)
RUN php artisan config:clear

# Storage symlink
RUN php artisan storage:link

# Fix permissions
RUN chmod -R 777 storage bootstrap/cache

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
