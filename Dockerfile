FROM php:8.2-apache

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    default-mysql-client \
    libicu-dev \
    ffmpeg \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && docker-php-ext-install zip

# Копируем код приложения
COPY . /var/www

WORKDIR /var/www

COPY ./apache/vhost.conf /etc/apache2/sites-available/000-default.conf

COPY wait-for-mysql.sh /usr/local/bin/wait-for-mysql.sh
RUN chmod +x /usr/local/bin/wait-for-mysql.sh

RUN a2enmod rewrite

RUN mkdir -p /var/www/storage && chmod -R 777 /var/www/storage

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --prefer-source
