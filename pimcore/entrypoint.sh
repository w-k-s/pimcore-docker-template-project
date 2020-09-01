#!/usr/bin/env bash

echo "Waiting for Database Connection";
./wait-for-it.sh $(echo "$DB_HOST:$DB_PORT") -- ./vendor/bin/pimcore-install --ignore-existing-config --verbose;

# Serve from this directory (sets chdir on php-fpm; https://www.php.net/manual/en/install.fpm.configuration.php)
cd /usr/src/app/web

echo "Starting php-fpm";
php-fpm -F