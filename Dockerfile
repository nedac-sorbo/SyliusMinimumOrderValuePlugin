ARG PHP_VERSION=7.4
ARG NODE_VERSION=13
ARG NGINX_VERSION=1.21

##################################################
#                     PHP                        #
##################################################
FROM php:${PHP_VERSION}-fpm-alpine AS sylius_minimum_order_value_plugin_php

# persistent / runtime deps
RUN apk add --no-cache \
                acl \
                bash \
                file \
                gettext \
                git \
                gnupg \
                mariadb-client \
                openssh-client \
                libxml2 \
                libuuid \
                bind-tools \
                jq \
                py3-pip \
        ;

ARG XDEBUG_VERSION=3.0.4

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
        pecl clear-cache; \
        docker-php-ext-enable \
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    wget -O phive.phar https://phar.io/releases/phive.phar; \
    wget -O phive.phar.asc https://phar.io/releases/phive.phar.asc; \
    gpg --keyserver hkps://keyserver.openpgp.org --recv-keys 0x9D8A98B29B2D5D79; \
    gpg --verify phive.phar.asc phive.phar; \
    chmod +x phive.phar; \
    mv phive.phar /usr/local/bin/phive

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY docker/php-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /srv
ARG SYLIUS_VERSION=1.9

# TODO: Install using composer
RUN git clone --depth 1 --single-branch --branch "$SYLIUS_VERSION" https://github.com/Sylius/Sylius-Standard.git sylius

WORKDIR /srv/sylius

RUN set -eux; \
    pip install yq; \
    yq -y -i '.imports[.imports|length] |= . + {"resource": "../vendor/nedac/sylius-minimum-order-value-plugin/src/Resources/config/services_test.xml"}' config/services_test.yaml; \
    yq -y -i '.imports[.imports|length] |= . + "vendor/nedac/sylius-minimum-order-value-plugin/tests/Behat/Resources/suites.yml"' behat.yml.dist; \
    yq -y -i '.default.extensions."Behat\\MinkExtension".base_url = "http://localhost/"' behat.yml.dist; \
    yq -y -i '.default.extensions."FriendsOfBehat\\SuiteSettingsExtension".paths = ["vendor/nedac/sylius-minimum-order-value-plugin/features"]' behat.yml.dist

ARG PRIVATE_FLEX="false"
RUN set -eux; \
    if [ -z "$PRIVATE_FLEX" ] && [ "$PRIVATE_FLEX" != "false" ]; then \
        cat composer.json | jq --indent 4 '. * {"extra":{"symfony":{"allow-contrib":true,"endpoint":"http://localhost:8080"}}}' > composer.json.tmp; \
        mv composer.json.tmp composer.json; \
        cat composer.json | jq --indent 4 '. * {"config":{"secure-http":false}}' > composer.json.tmp; \
        mv composer.json.tmp composer.json; \
    fi

ARG PLUGIN_VERSION=dev-master
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
    composer require nedac/sylius-minimum-order-value-plugin:"$PLUGIN_VERSION" --no-progress -vvv; \
    composer recipes:install nedac/sylius-minimum-order-value-plugin --force -n; \
    composer clear-cache; \
    cat src/Entity/Channel/Channel.php

VOLUME /srv/sylius/var

VOLUME /srv/sylius/public/media

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

##################################################
#                     NODEJS                     #
##################################################
FROM node:${NODE_VERSION}-alpine AS sylius_minimum_order_value_plugin_nodejs

WORKDIR /srv/sylius

RUN set -eux; \
        apk add --no-cache --virtual .build-deps \
                g++ \
                gcc \
                git \
                make \
                python2 \
        ;

COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/package.json ./package.json
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/gulpfile.babel.js ./gulpfile.babel.js
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/.babelrc ./.babelrc
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/.eslintrc.js ./.eslintrc.js
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/gulpfile.babel.js ./vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/gulpfile.babel.js
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Resources/private vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Resources/private/
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/gulpfile.babel.js ./vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/gulpfile.babel.js
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/private vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/private/
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/sylius/sylius/src/Sylius/Bundle/UiBundle/Resources/private vendor/sylius/sylius/src/Sylius/Bundle/UiBundle/Resources/private/
COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/vendor/nedac/sylius-minimum-order-value-plugin/src/Resources/public vendor/nedac/sylius-minimum-order-value-plugin/src/Resources/public/

RUN sed -i 's/node: true,/node: true,\n    browser: true/g' .eslintrc.js

RUN set -eux; \
    yarn install; \
    yarn cache clean

RUN yarn build

COPY docker/nodejs-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["yarn", "watch"]

##################################################
#                     NGINX                      #
##################################################
FROM nginx:${NGINX_VERSION}-alpine AS sylius_minimum_order_value_plugin_nginx

RUN apk add --no-cache bash

COPY docker/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/sylius

COPY --from=sylius_minimum_order_value_plugin_php /srv/sylius/public public/
COPY --from=sylius_minimum_order_value_plugin_nodejs /srv/sylius/public public/

COPY docker/wait-for-it.sh /
RUN chmod +x /wait-for-it.sh

CMD /wait-for-it.sh -t 0 127.0.0.1:9000 -- nginx -g "daemon off;"

##################################################
#                     CHROME                     #
##################################################
FROM ubuntu:focal AS sylius_minimum_order_value_plugin_chrome

ARG DEBIAN_FRONTEND=noninteractive

RUN set -eux; \
    apt update; \
    apt install -yqq \
        wget \
        gnupg \
        xvfb \
        pulseaudio \
        x11vnc \
        fluxbox \
        libfontconfig \
        libfreetype6 \
        xfonts-cyrillic \
        xfonts-scalable \
        fonts-liberation \
        fonts-ipafont-gothic \
        fonts-wqy-zenhei \
        fonts-tlwg-loma-otf \
        ttf-ubuntu-font-family \
    ; \
    wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add -; \
    wget -q https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb; \
    apt install -yqq ./google-chrome-stable_current_amd64.deb; \
    apt install -yqq google-chrome-stable; \
    apt clean

COPY docker/start-xvfb.sh /opt/bin/start-xvfb.sh
RUN chmod ugo+x /opt/bin/start-xvfb.sh

COPY docker/start-vnc.sh /opt/bin/start-vnc.sh
RUN chmod ugo+x /opt/bin/start-vnc.sh

ENV SCREEN_WIDTH 1360
ENV SCREEN_HEIGHT 1020
ENV SCREEN_DEPTH 24
ENV SCREEN_DPI 96
ENV DISPLAY :99.0
ENV DISPLAY_NUM 99

COPY docker/chrome-entrypoint.sh /opt/bin/entrypoint.sh
RUN chmod ugo+x /opt/bin/entrypoint.sh

ENTRYPOINT ["/opt/bin/entrypoint.sh"]
