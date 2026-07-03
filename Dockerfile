FROM wordpress:6.5-php8.2-apache

# Use the default production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Increase upload limits and memory limit (critical for WooCommerce and heavy media uploads)
RUN { \
    echo 'upload_max_filesize = 128M'; \
    echo 'post_max_size = 128M'; \
    echo 'memory_limit = 512M'; \
    echo 'max_execution_time = 300'; \
} > /usr/local/etc/php/conf.d/wordpress-custom.ini

# Copy all project files into the Apache web root
COPY . /var/www/html/

# Ensure correct file permissions for Apache (www-data)
RUN chown -R www-data:www-data /var/www/html/
