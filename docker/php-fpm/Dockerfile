FROM php:fpm-alpine

RUN apk add --no-cache --update --virtual buildDeps \
    autoconf \
    pcre-dev \
    gcc \
    make \
    g++ \
    zlib-dev

RUN pecl install redis
RUN docker-php-ext-enable redis
COPY redis.ini /usr/local/etc/php/conf.d/redis.ini

ARG XDEBUG_ON
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN if [ $XDEBUG_ON -eq 1 ]; then \
 pecl install xdebug \
 docker-php-ext-enable xdebug; \
 fi

RUN apk del buildDeps

RUN addgroup -g 1000 -S docker-www-data; \
    adduser -u 1000 -D -S -G docker-www-data docker-www-data

USER docker-www-data

EXPOSE 9000
CMD ["php-fpm"]
