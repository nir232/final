#!/bin/sh
set -eu

mkdir -p /var/www/html/data
chown -R www-data:www-data /var/www/html/data

php /var/www/html/init_db.php

# init_db.php creates the SQLite file as root; Apache runs as www-data.
chown -R www-data:www-data /var/www/html/data

exec apache2-foreground
