# Use PHP 8.1 with Apache
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy Laravel files into the container
COPY . .

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Set permissions for Laravel
RUN chmod -R 777 storage bootstrap/cache

# Expose port 10000 for Laravel
EXPOSE 10000

# Start Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
