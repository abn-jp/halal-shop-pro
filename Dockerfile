# Halal Shop Pro — WordPress on Railway
# Extends the official WordPress image and pre-installs our theme

FROM wordpress:6.5-php8.2-apache

LABEL maintainer="abn-jp"
LABEL description="Halal Shop Pro — WordPress WooCommerce Theme"

# Copy our theme into the WordPress themes directory
COPY . /var/www/html/wp-content/themes/halal-shop-pro/

# Set correct ownership and permissions
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/halal-shop-pro \
    && chmod -R 755 /var/www/html/wp-content/themes/halal-shop-pro

# Fix MPM conflict: delete every mpm_* symlink, then wire up only prefork + rewrite
RUN find /etc/apache2/mods-enabled/ -name "mpm_*" -delete \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && a2enmod rewrite

# Custom PHP settings for WooCommerce
RUN echo "upload_max_filesize = 64M\n\
post_max_size = 64M\n\
memory_limit = 512M\n\
max_execution_time = 300\n\
max_input_vars = 3000" > /usr/local/etc/php/conf.d/wordpress-custom.ini

EXPOSE 80
