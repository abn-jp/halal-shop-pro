# Halal Shop Pro — WordPress on Railway
# Extends the official WordPress image and pre-installs our theme

FROM wordpress:6.5-php8.2-apache

LABEL maintainer="abn-jp"
LABEL description="Halal Shop Pro — WordPress WooCommerce Theme"

# Copy theme (exclude wrapper script from theme directory via .dockerignore)
COPY . /var/www/html/wp-content/themes/halal-shop-pro/

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/halal-shop-pro \
    && chmod -R 755 /var/www/html/wp-content/themes/halal-shop-pro

# Enable rewrite (prefork already active in base image)
RUN a2enmod rewrite

# Custom PHP settings for WooCommerce
RUN echo "upload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 512M\nmax_execution_time = 300\nmax_input_vars = 3000" \
    > /usr/local/etc/php/conf.d/wordpress-custom.ini

# Runtime wrapper: fixes MPM conflict BEFORE Apache starts
COPY docker-entrypoint-wrapper.sh /usr/local/bin/docker-entrypoint-wrapper.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-wrapper.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint-wrapper.sh"]
CMD ["apache2-foreground"]
