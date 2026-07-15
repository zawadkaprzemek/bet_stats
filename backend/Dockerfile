FROM composer:2 AS composer

FROM php:8.4-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        libicu-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        default-mysql-client \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        dom \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        simplexml \
        xml \
        xmlwriter \
        zip \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && groupmod -o -g "${GID}" www-data \
    && usermod -o -u "${UID}" -g "${GID}" www-data \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/app

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
