FROM php:cli-alpine

RUN curl -s http://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/ && \
    ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN addgroup -g 1000 -S php-cli && \
    adduser -u 1000 -D -S -G php-cli php-cli

USER php-cli

WORKDIR /var/www/html

# Hackish way to keep the container running
CMD ["tail", "-f", "/dev/null"]
