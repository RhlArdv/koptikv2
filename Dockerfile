FROM dunglas/frankenphp:php8.4

WORKDIR /app

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install node + npm
RUN apt-get update && apt-get install -y nodejs npm

# install php extensions
RUN install-php-extensions pdo_mysql gd redis zip

COPY . .

# install php deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# build vite assets
RUN npm install
RUN npm run build

# laravel cleanup
RUN php artisan config:clear

# permissions
RUN chmod -R 777 storage bootstrap/cache

# storage symlink
RUN php artisan storage:link

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
