FROM php:8.5-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    mysql-client

# Install PHP extensions
# Note: opcache comes preinstalled in PHP 8.5, only install pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP-FPM
# Using default TCP configuration (127.0.0.1:9000) which works better in Docker

WORKDIR /var/www/html

# Create entrypoint script
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'if [ ! -d "vendor" ]; then' >> /entrypoint.sh && \
    echo '  echo "Installing Composer dependencies..."' >> /entrypoint.sh && \
    echo '  composer install --no-interaction' >> /entrypoint.sh && \
    echo 'fi' >> /entrypoint.sh && \
    echo 'exec /usr/local/sbin/php-fpm -F' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

EXPOSE 9000

CMD ["/entrypoint.sh"]

