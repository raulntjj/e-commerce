FROM php:8.2-fpm-alpine

WORKDIR /app

RUN apk update && apk add --no-cache \
    build-base \
    autoconf \
    libzip-dev \
    zip \
    zlib-dev \
    libpng-dev \
    oniguruma-dev \
    curl-dev \
    libxml2-dev \
    postgresql-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install extensions separately to isolate failures
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis

RUN docker-php-ext-install pdo_mysql bcmath zip opcache

# Special handling for sockets
RUN docker-php-ext-configure sockets --enable-sockets=no \
    || docker-php-ext-install sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

COPY . .

RUN chown -R www-data:www-data /app

EXPOSE 9000

CMD ["php-fpm"]