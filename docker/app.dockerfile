FROM php:8.2-fpm

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    IPE_GD_WITHOUTAVIF=1 install-php-extensions redis mysqli pdo_mysql mbstring zip exif pcntl gd opcache @composer

COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN usermod -u 1000 www-data
USER www-data

# Install composer
EXPOSE 9000
CMD ["php-fpm"]
