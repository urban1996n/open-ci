#!/bin/sh
set -e

#[Run composer install]
php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && composer install --no-dev --no-interaction --no-ansi --no-scripts --no-progress \
    && rm -rf /root/.composer

#[Run supervisor service]
supervisord -c /etc/supervisor/supervisord.conf

#[Run php fpm listen]
/usr/local/sbin/php-fpm
