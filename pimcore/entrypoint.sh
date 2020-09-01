#!/usr/bin/env bash

echo "Waiting for Database Connection";
./wait-for-it.sh $(echo "$DB_HOST:$DB_PORT") -- ./vendor/bin/pimcore-install --ignore-existing-config --verbose;

echo "Starting php-fpm";
php-fpm -F