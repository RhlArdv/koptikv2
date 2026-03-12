FROM dunglas/frankenphp:php8.4

WORKDIR /app

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install extensions
RUN install-php-extensions pdo_mysql gd redis zip

# copy project
COPY . .

# install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
