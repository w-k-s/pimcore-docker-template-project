FROM php:7.4-fpm

ENV DB_USER=
ENV DB_PASSWORD=
ENV DB_HOST=
ENV DB_PORT=3306
ENV DB_NAME=
ENV PIMCORE_INSTALL_ADMIN_USERNAME=admin
ENV PIMCORE_INSTALL_ADMIN_PASSWORD=password

# Required apcu zip extension
RUN pecl install apcu

# Required PHP zip extension
RUN apt-get update && \
apt-get install -y \
libzip-dev

# Download script to install PHP extensions and dependencies
# Source: https://github.com/chialab/docker-php/blob/master/7.4/fpm/Dockerfile
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN DEBIAN_FRONTEND=noninteractive apt-get update -q \
    && DEBIAN_FRONTEND=noninteractive apt-get install -qq -y \
      curl \
      git \
      zip unzip \
    && install-php-extensions \
      bcmath \
      bz2 \
      calendar \
      exif \
      gd \
      intl \
      ldap \
      memcached \
      mysqli \
      opcache \
      pdo_mysql \
      #pdo_pgsql \
      #pgsql \
      redis \
      soap \
      xsl \
      zip \
      sockets
# already installed:
#      iconv \
#      mbstring \


# Copy Composer from Composer Official Image
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/app

# Copy Project into /usr/src/app
COPY --chown=1000:1000 . /usr/src/app

# Install Depdencies via Composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install -v 

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

CMD ./wait-for-it.sh $(echo "$DB_HOST:$DB_PORT") -- ./vendor/bin/pimcore-install --ignore-existing-config --verbose

EXPOSE 9000