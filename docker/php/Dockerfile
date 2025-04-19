FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    zlib1g-dev libzip-dev unzip \
    libjpeg-dev libpng-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo_mysql zip gd

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

WORKDIR /var/www
