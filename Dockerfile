FROM dunglas/frankenphp:php8.4

WORKDIR /app

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install node
RUN apt-get update && apt-get install -y nodejs npm

# install php extensions
RUN install-php-extensions pdo_mysql gd redis zip

COPY . .

# install php dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# install node dependencies
RUN npm install

# build vite assets
RUN npm run build

# clear cache
RUN php artisan config:clear
RUN php artisan cache:clear

# fix permissions
RUN chmod -R 777 storage bootstrap/cache

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
