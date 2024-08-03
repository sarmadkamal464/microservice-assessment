FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    librabbitmq-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN curl -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip amqp

    # Install PHP extensions
RUN pecl install amqp
RUN docker-php-ext-enable amqp

# Verify that the amqp extension is installed
RUN php -m | grep amqp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

RUN chown -R www-data:www-data /var/www/html

#ENV COMPOSER_ALLOW_SUPERUSER=1

#RUN composer clear-cache
# Install packages with composer
#RUN composer install --no-interaction --prefer-dist --optimize-autoloader || (echo "Composer install failed" && exit 1)

# Add wait-for-it script to wait for RabbitMQ to be ready
RUN curl -o /usr/local/bin/wait-for-it https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh && \
    chmod +x /usr/local/bin/wait-for-it

# Add the entry point script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use entry point instead of CMD
ENTRYPOINT ["docker-entrypoint.sh"]