FROM dunglas/frankenphp:php8.4

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_mysql gd redis zip

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php artisan config:clear
RUN php artisan config:cache

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
