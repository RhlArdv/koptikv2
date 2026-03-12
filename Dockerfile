FROM dunglas/frankenphp:php8.4

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_mysql gd redis zip

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN npm install
RUN npm run build

RUN php artisan config:clear

RUN chmod -R 777 storage bootstrap/cache

RUN php artisan storage:link

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
