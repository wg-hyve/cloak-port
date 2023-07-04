FROM composer:latest
LABEL Maintainer="SineMah"


ARG UNAME=www-docker
ARG UID=1000
ARG GID=1000

# Setup document root
WORKDIR /var/www

# Add user for application
RUN addgroup -g $GID $UNAME
RUN adduser -u $UID -G $UNAME -h /home/$UNAME -D $UNAME

# Create symlink so programs depending on php still function
RUN ln -s /usr/bin/php82 /usr/bin/php
RUN ln -s /usr/bin/pecl82 /usr/bin/pecl

RUN apk --update --no-cache add autoconf g++ musl-dev make linux-headers && \
pecl install -f xdebug && \
apk del --purge autoconf g++ make

# Configure PHP-FPM
COPY config/php.ini /etc/php82/conf.d/custom.ini

USER $UNAME