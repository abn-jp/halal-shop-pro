# Halal Shop Pro — WordPress on Railway
# Extends the official WordPress image and pre-installs our theme

FROM wordpress:6.5-php8.2-apache

LABEL maintainer="abn-jp"
LABEL description="Halal Shop Pro — WordPress WooCommerce Theme"

# Copy theme into WordPress themes directory
COPY . /var/www/html/wp-content/themes/halal-shop-pro/

# Install must-use plugin for Railway URL fix & cache bypass
# (the file lives inside the theme repo; we copy it to mu-plugins/)
RUN mkdir -p /var/www/html/wp-content/mu-plugins \
    && cp /var/www/html/wp-content/themes/halal-shop-pro/mu-plugins/halal-lang-fix.php \
          /var/www/html/wp-content/mu-plugins/halal-lang-fix.php

# Set correct ownership and permissions
RUN chown -R www-data:www-data \
        /var/www/html/wp-content/themes/halal-shop-pro \
        /var/www/html/wp-content/mu-plugins \
    && chmod -R 755 \
        /var/www/html/wp-content/themes/halal-shop-pro \
        /var/www/html/wp-content/mu-plugins

# Enable rewrite (prefork already active in base image)
RUN a2enmod rewrite

# Custom PHP settings for WooCommerce
RUN echo "upload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 512M\nmax_execution_time = 300\nmax_input_vars = 3000" \
    > /usr/local/etc/php/conf.d/wordpress-custom.ini

# Write the MPM-fix wrapper inline so no external file is needed
RUN printf '#!/bin/bash\nset -e\nfind /etc/apache2/mods-enabled/ -name "mpm_*" -delete 2>/dev/null || true\nln -sf 