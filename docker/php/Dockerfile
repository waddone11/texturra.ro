FROM php:8.3-fpm

# System dependencies for the PHP extensions required by composer.json:
# intervention/image v3 (gd), phpspreadsheet (gd/zip/xml), mysql, intl, etc.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        zip \
        intl \
        bcmath \
        exif \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Run as a non-root user matching the host (avoids permission issues on mounted volume)
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g "${GID}" www-data \
    && usermod  -o -u "${UID}" -g "${GID}" www-data
USER www-data

EXPOSE 9000
CMD ["php-fpm"]
