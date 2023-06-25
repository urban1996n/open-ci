FROM php:8.2-fpm-alpine

#[This will be the feed for supervisor's runners count]
ARG RUNNERS_COUNT
ARG REDIS_PASSWORD
ARG RABBITMQ_USER
ARG RABBITMQ_PASSWORD

ENV HAVE_LINUX_SOCK_DIAG_H=1
ENV RUNNERS_COUNT="$RUNNERS_COUNT"
ENV REDIS_PASSWORD="$REDIS_PASSWORD"
ENV AMQP_USER="$RABBITMQ_USER"
ENV AMQP_PASSWORD="$RABBITMQ_PASSWORD"

RUN apk update \
    && apk upgrade \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache linux-headers

#[Install redis extension]
RUN pecl install redis

#[Add and install required libraries and extensions]
RUN apk add --no-cache \
        git \
        unzip \
        libzip-dev \
        && docker-php-ext-install \
        ctype \
        opcache \
        zip \
        sockets\
        sysvsem\
        && docker-php-ext-enable\
        ctype \
        opcache \
        zip \
        sockets\
        sysvsem \
        redis

#[Install supervisor service, to look after runners processes]
RUN apk add supervisor

#[Install openrc to add supervisor to default runtime processes/services]
RUN apk add openrc

#[Create supervisor's configuration directory]
RUN mkdir -p /etc/supervisor/conf.d;

#[Create supervisor's logs directory]
    RUN mkdir -p  /var/log/supervisord;

#[Add configuration file for workers processes]
COPY supervisor/supervisor.program.conf /etc/supervisor/conf.d/ci-cd-worker.conf

#[Enable $RUNNERS_COUNT processes at once, and watch them (retry if requrired)]
RUN echo "numprocs=$RUNNERS_COUNT" >> /etc/supervisor/conf.d/ci-cd-worker.conf

#[Add supervisor main configuration]
COPY supervisor/supervisor.conf /etc/supervisor/supervisord.conf

#[Cleanup]
RUN apk del .phpize-deps

COPY ./docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh
WORKDIR /var/app

RUN mkdir exec

ENTRYPOINT ["/docker-entrypoint.sh"]
