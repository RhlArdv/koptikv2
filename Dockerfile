FROM dunglas/frankenphp:php8.4

WORKDIR /app

COPY . .

RUN install-php-extensions pdo_mysql gd redis

RUN composer install --optimize-autoloader --no-interaction

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
