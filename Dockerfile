FROM wordpress:6.5-php8.2-apache

# Use the default production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Increase upload limits and memory limit (critical for WooCommerce and heavy media uploads)
RUN { \
    echo 'upload_max_filesize = 512M'; \
    echo 'post_max_size = 512M'; \
    echo 'memory_limit = 1024M'; \
    echo 'max_execution_time = 600'; \
} > /usr/local/etc/php/conf.d/wordpress-custom.ini


# Enable Apache mod_rewrite — REQUIRED for WordPress pretty permalinks.
# Without this, URLs will appear as /index.php/page-name instead of /page-name.
RUN a2enmod rewrite

# Allow .htaccess overrides in the web root (AllowOverride All)
# This is necessary so Apache respects the WordPress .htaccess rewrite rules.
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

# Copy all project files into the Apache web root
COPY . /var/www/html/

# Ensure correct file permissions for Apache (www-data)
# We avoid heavy 'find chmod' commands which duplicate layers and consume huge disk space.
RUN chown -R www-data:www-data /var/www/html/ \
    && if [ -f /var/www/html/.htaccess ]; then chmod 644 /var/www/html/.htaccess; fi

