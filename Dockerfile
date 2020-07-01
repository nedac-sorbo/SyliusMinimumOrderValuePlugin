ARG PHP_VERSION=7.4
ARG NODE_VERSION=11
ARG NGINX_VERSION=1.17

FROM php:${PHP_VERSION}-fpm-alpine AS sylius_minimum_order_value_plugin_php

# persistent / runtime deps
RUN apk add --no-cache \
                acl \
                bash \
                file \
                gettext \
                git \
                mariadb-client \
                openssh-client \
                libxml2 \
                libuuid \
                bind-tools \
        ;

ARG XDEBUG_VERSION=2.9.2
ARG REDIS_VERSION=5.0.2
ARG UUID_VERSION=1.0.4

RUN set -eux; \
        apk add --no-cache --virtual .build-deps \
                $PHPIZE_DEPS \
                coreutils \
                freetype-dev \
                icu-dev \
                libjpeg-turbo-dev \
                libpng-dev \
                libtool \
                libwebp-dev \
                libzip-dev \
                mariadb-dev \
                zlib-dev \
                libxml2-dev \
                util-linux-dev \
        ; \
        \
        docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype; \
        docker-php-ext-configure zip --with-zip; \
        docker-php-ext-install -j$(nproc) \
                exif \
                gd \
                intl \
                pdo_mysql \
                zip \
                bcmath \
                sockets \
                soap \
        ; \
       pecl install xdebug-${XDEBUG_VERSION}; \
        pecl install redis-${REDIS_VERSION}; \
        pecl install uuid-${UUID_VERSION}; \
        pecl clear-cache; \
        docker-php-ext-enable \
                opcache \
                redis \
                uuid \
                xdebug \
        ; \
        \
        runDeps="$( \
                scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
                        | tr ',' '\n' \
                        | sort -u \
                        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )"; \
        apk add --no-cache --virtual .sylius-phpexts-rundeps $runDeps; \
        \
        apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    echo "memory_limit=4G" >> /usr/local/etc/php/php-cli.ini; \
        composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
        composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY docker/php-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /srv/sylius/tests/Application

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


FROM node:${NODE_VERSION}-alpine AS sylius_minimum_order_value_plugin_nodejs

WORKDIR /srv/sylius

RUN set -eux; \
        apk add --no-cache --virtual .build-deps \
                g++ \
                gcc \
                git \
                make \
                python \
        ;

COPY docker/nodejs-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["yarn", "--cwd=/srv/sylius/tests/Application", "watch"]

FROM nginx:${NGINX_VERSION}-alpine AS sylius_minimum_order_value_plugin_nginx

COPY docker/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/sylius

RUN apk add --no-cache bash

COPY docker/wait-for-it.sh /
RUN chmod +x /wait-for-it.sh

CMD /wait-for-it.sh -t 0 127.0.0.1:9000 -- nginx -g "daemon off;"
