ARG PHP_VERSION=8.4

# Base runtime with Apache
FROM php:${PHP_VERSION}-apache AS base

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libzip-dev; \
    docker-php-ext-install zip opcache; \
    a2enmod rewrite; \
    rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# Development image
FROM base AS development
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/www/html
RUN composer install --prefer-dist --no-interaction --no-progress
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var
ENV APP_ENV=dev
ENV APP_DEBUG=1
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -fsS http://localhost/v1/search/test/1/1 || exit 1

# Production image
FROM base AS production
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY . /var/www/html
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var
ENV APP_ENV=prod
ENV APP_DEBUG=0
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -fsS http://localhost/v1/search/test/1/1 || exit 1
