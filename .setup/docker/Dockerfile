FROM php:8.2-apache

WORKDIR /var/www/html

# System deps
RUN apt-get update && apt-get install -y \
    libzip-dev \
 && docker-php-ext-install mysqli opcache \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# PHP hardening
RUN { \
  echo "opcache.enable=1"; \
  echo "opcache.validate_timestamps=0"; \
  echo "expose_php=0"; \
} > /usr/local/etc/php/conf.d/prod.ini

# Copy app
COPY --chown=www-data:www-data app/ .

# Healthcheck
HEALTHCHECK CMD curl -f http://localhost/ || exit 1

USER www-data
CMD ["apache2-foreground"]
