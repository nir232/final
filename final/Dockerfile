# PHP + Apache runtime for the existing hotel booking application.
FROM php:8.2-apache

# SQLite is self-contained, so no MySQL host/user/password is required.
RUN docker-php-ext-install pdo_sqlite

COPY . /var/www/html/

RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod +x /var/www/html/render-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/var/www/html/render-entrypoint.sh"]
