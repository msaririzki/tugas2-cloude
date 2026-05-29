FROM docker.io/library/php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends nano \
    && docker-php-ext-install mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
