# Start from the official PHP image that runs PHP-FPM
# (PHP-FPM = the PHP process that Nginx sends .php requests to)
FROM php:8.4-fpm

# Install the extension PHP needs to talk to MySQL through PDO
RUN docker-php-ext-install pdo_mysql

# The folder inside the container where our code lives
WORKDIR /var/www/html

# Our PHP settings (hide errors from users, log them instead)
COPY php/app.ini /usr/local/etc/php/conf.d/app.ini