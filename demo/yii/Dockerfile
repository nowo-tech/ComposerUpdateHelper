FROM php:8.5-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    nginx \
    supervisor \
    mysql-client

# Install PHP extensions
# Note: opcache comes preinstalled in PHP 8.5, only install pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
# Using default TCP configuration (127.0.0.1:9000) which works better in Docker

# Configure supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Create entrypoint script
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'if [ ! -d "vendor" ]; then' >> /entrypoint.sh && \
    echo '  echo "Installing Composer dependencies..."' >> /entrypoint.sh && \
    echo '  composer install --no-interaction' >> /entrypoint.sh && \
    echo 'fi' >> /entrypoint.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]

