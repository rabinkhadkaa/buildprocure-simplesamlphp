# =========================================
# SimpleSAMLphp Dockerfile (Using local composer install)
# =========================================

FROM php:8.2-apache

# Install required packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    git \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql zip intl bcmath dom mbstring xml opcache

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy entire project into container
COPY . /var/www/app
WORKDIR /var/www/app

# Set Apache DocumentRoot to SimpleSAMLphp public folder
# Replace the default virtual host with our custom config
COPY simplesamlphp-config.conf /etc/apache2/sites-available/simplesamlphp.conf

RUN a2ensite simplesamlphp.conf
RUN a2dissite 000-default.conf

# Prepare directories for SimpleSAMLphp
RUN mkdir -p /tmp/simplesaml /var/www/app/simplesamlphp/log /var/cache/simplesamlphp && \
    chmod -R 777 /tmp/simplesaml /var/www/app/simplesamlphp/log /var/cache/simplesamlphp && \
    chown -R www-data:www-data /tmp/simplesaml /var/www/app/simplesamlphp/log /var/cache/simplesamlphp

# PHP session config for SimpleSAMLphp
RUN echo "session.save_path=/tmp/simplesaml" > /usr/local/etc/php/conf.d/sessions.ini \
 && echo "session.name=SimpleSAMLSESSID" > /usr/local/etc/php/conf.d/simplesaml_name.ini

# Avoid Apache warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]
