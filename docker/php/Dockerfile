FROM php:7.4.7-fpm-alpine

ARG LOCAL_ENV
ARG USER_ID
ARG GROUP_ID

RUN apk update && apk add --no-cache \
    libxslt-dev \
    git \
    curl \
    g++ \
    gcc \
    tar \
    zip \
    wget \
    unzip \
    openssh \
    libjpeg-turbo-dev \
    libmcrypt-dev \
    libmcrypt \
    libpng-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    shadow

RUN docker-php-ext-configure gd \
        -with-freetype=/usr/include/ \
#        -with-png=/usr/png/ \
        -with-jpeg=/usr/jpeg/

RUN docker-php-ext-install \
        mbstring \
        gd \
        tokenizer \
        opcache \
        exif \
        xsl \
        zip \
        pdo \
        pdo_mysql \
        bcmath

RUN if [ "${LOCAL_ENV}" = "dev" ] ; then apk add --no-cache $PHPIZE_DEPS \
&& pecl install xdebug-2.9.1 \
&& docker-php-ext-enable xdebug ; fi

RUN apk add --no-cache icu-dev \
    && docker-php-ext-install intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /var/log/php

# Set IDs from our local user
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data || true
USER "${USER_ID}:${GROUP_ID}"

COPY ./docker/php/php.ini         /usr/local/etc/php/conf.d/php.ini
COPY ./docker/php/php-fpm.conf    /usr/local/etc/php-fpm.d/php-fpm.conf

# COPY . /app
