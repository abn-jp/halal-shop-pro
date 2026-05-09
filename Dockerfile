# Halal Shop Pro — WordPress on Railway
# Extends the official WordPress image and pre-installs our theme

FROM wordpress:6.5-php8.2-apache

LABEL maintainer="abn-jp"
LABEL description="Halal Shop Pro — WordPress WooCommerce Theme"

# Copy theme into WordPress themes directory
COPY . /var/www/html/wp-content/themes/halal-shop-pro/

# Install must-use plugin for Railway URL fix & cache bypass
RUN mkdir -p /var/www/html/wp-content/mu-plugins \
    && cp /var/www/html/wp-content/themes/halal-shop-pro/mu-plugins/halal-lang-fix.php \
          /var/www/html/wp-content/mu-plugins/halal-lang-fix.php

# Install unzip (not in base wordpress image) then download & bundle WooCommerce
RUN apt-get update && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/www/html/wp-content/plugins \
    && curl -sL https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip \
         -o /tmp/woocommerce.zip \
    && unzip -q /tmp/woocommerce.zip -d /var/www/html/wp-content/plugins/ \
    && rm /tmp/woocommerce.zip

# Set correct ownership and permissions
RUN chown -R www-data:www-data \
        /var/www/html/wp-content/themes/halal-shop-pro \
        /var/www/html/wp-content/mu-plugins \
        /var/www/html/wp-content/plugins/woocommerce \
    && chmod -R 755 \
        /var/www/html/wp-content/themes/halal-shop-pro \
        /var/www/html/wp-content/mu-plugins \
        /var/www/html/wp-content/plugins/woocommerce

# Enable rewrite (prefork already active in base image)
RUN a2enmod rewrite

# Custom PHP settings for WooCommerce
RUN echo "upload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 512M\nmax_execution_time = 300\nmax_input_vars = 3000" \
    > /usr/local/etc/php/conf.d/wordpress-custom.ini

# Write the MPM-fix wrapper inline so no external file is needed
RUN printf '#!/bin/bash\nset -e\nfind /etc/apache2/mods-enabled/ -name "mpm_*" -delete 2>/dev/null || true\nln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf\nln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load\nexec docker-entrypoint.sh "$@"\n' \
    > /usr/local/bin/docker-entrypoint-wrapper.sh \
    && chmod +x /usr/local/bin/docker-entrypoint-wrapper.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint-wrapper.sh"]
CMD ["apache2-foreground"]
