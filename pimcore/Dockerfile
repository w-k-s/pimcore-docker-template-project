FROM php:7.4-fpm

# Required apcu zip extension
RUN pecl install apcu

# Required PHP zip extension
RUN apt-get update && \
apt-get install -y \
libzip-dev

# Install PHP Extensions
# RUN docker-php-ext-install pdo_mysql
# RUN docker-php-ext-install zip
# RUN docker-php-ext-enable apcu

# # Extensions required by PimCore
# RUN docker-php-ext-install exif
# RUN docker-php-ext-enable exif
# RUN docker-php-ext-configure intl
# RUN docker-php-ext-install intl

# # -- Install Gd 
# RUN apt-get update && apt-get install -y \
#         libfreetype6-dev \
#         libjpeg62-turbo-dev \
#         libpng-dev \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install -j$(nproc) gd

# Download script to install PHP extensions and dependencies
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
      pdo_pgsql \
      pgsql \
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

RUN ls -a 

# Install Depdencies via Composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install -v 

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

EXPOSE 9000