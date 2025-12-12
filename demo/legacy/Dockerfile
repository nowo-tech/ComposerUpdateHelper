FROM php:7.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    nginx \
    supervisor \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.owner = www-data/listen.owner = www-data/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.group = www-data/listen.group = www-data/' /usr/local/etc/php-fpm.d/www.conf

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

