#!/bin/sh
set -e

if [ -f /usr/local/apache2/conf/ca_bundle.crt ]
then
  sed -i s/#SSLCertificateChainFile/SSLCertificateChainFile/g /usr/local/apache2/conf/project.conf
fi

#[Run composer install]
php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && composer install --no-dev --no-interaction --no-ansi --no-scripts --no-progress \
    && rm -rf /root/.composer

#[Run supervisor service]
supervisord -c /etc/supervisor/supervisord.conf

#[Run php fpm listen]
/usr/local/sbin/php-fpm
