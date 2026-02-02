# Base image
FROM php:7.4-apache

# Install system deps
RUN apt-get update && apt-get install -y \
    unzip git curl \
 && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (caching)
COPY app/composer.json app/composer.lock ./

# Install PHP dependencies (prod only, optimized)
RUN composer install --no-dev --optimize-autoloader

# Copy rest of application
COPY app .

# Optional: set Apache permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Apache port
EXPOSE 80

# Default command
CMD ["apache2-foreground"]