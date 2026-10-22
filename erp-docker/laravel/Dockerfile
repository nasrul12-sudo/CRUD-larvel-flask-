FROM php:8.4-fpm-alpine

# Install dependency sistem + ekstensi PHP untuk Laravel
RUN apk add --no-cache \
        git \
        curl \
        bash \
        libpng-dev \
        oniguruma-dev \
        libxml2-dev \
        zip \
        unzip \
        icu-dev \
        mysql-client \
        linux-headers \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

EXPOSE 9000
