# PimCore Docker Template Project

This is a template project consisting of PimCore's `demo-ecommerce` project built to run in a docker container.
The project can be used to go through the Pimcore tutorial found [here](https://pimcore.com/docs/6.x/Development_Documentation).

## To run this project

### Prerequisites:
- Docker (with Container memory allocated to at least 8GB)
- Docker-Compose v3.7

### Steps

**Note**: This configuration is WIP. It currently does **NOT** work.
- Run the following command in Termal. The first time you run this command, it will take a **VERY** long time.

```sh 
docker-compose up
```

## Using a different Pimcore example package

Pimcore offers 2 example packages, described in their [getting started](https://pimcore.com/docs/6.x/Development_Documentation/Getting_Started/Installation.html) page: 

1. Skeleton Package (only for experienced Pimcore developers)
2. Demo Package (the one used in this repository)

The following steps explain how you could use a different package in this project:

## Steps

**Install Composer**
If you have composer installed, you can skip this section.
The instructions for this section are for OS X and were retrieved from [this](https://duvien.com/blog/installing-composer-mac-osx) resource.

1. Open a terminal and navigate to your user directory, ie 

  ```sh
  cd ~
  ```

2. Run this command shown below to download Composer. This will create a Phar (PHP Archive) file called `composer.phar`:

  ```sh
  curl -sS https://getcomposer.org/installer | php
  ```
3. Move `composer.phar` to `/usr/local/bin/`:

  ```sh
  sudo mv composer.phar /usr/local/bin/
  ```

4. We want to run Composer with having to be root al the time, so we need to change the permissions

  ```sh
  sudo chmod 755 /usr/local/bin/composer.phar
  ```

5. Next, we need to let Bash know where to execute Composer:  

  ```sh
  nano ~/.bash_profile
  ```

6. Add this line below to bash_profile and save

  ```sh
  alias composer="php /usr/local/bin/composer.phar"
  ```

7. Load the updated configuration on terminal:

  ```sh
  source ~/.bash_profile
  ```

8. Verify that composer has been installed using the following command:

  ```sh
  composer -v
  ```

**Download Project**
1. Once you have composer installed, delete the pimcore folder in the project root directory

  ```sh
  rm -rf pimcore
  ```

2. Use composer to install the pimcore example project of your choice. The project directory must be named `pimcore` to work with docker configurations.

  For instance if you'd like to to install the skeleton project, 

  ``` sh
  COMPOSER_MEMORY_LIMIT=-1 composer create-project pimcore/skeleton pimcore
  ```

3. Once the pimcore project has been downloaded, add the following dockerfile in the `pimcore` Directory:

  _`${PROJECT_ROOT}/pimcore/Dockerfile`_
  ```Dockerfile
  FROM php:7.4-fpm

  ENV DB_USER=
  ENV DB_PASSWORD=
  ENV DB_HOST=
  ENV DB_PORT=3306
  ENV DB_NAME=
  ENV ADMIN_USERNAME=admin
  ENV ADMIN_PASSWORD=pimcore

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

  RUN ./wait-for-it.sh $(echo "$DB_HOST:$DB_PORT")

  RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

  CMD ./vendor/bin/pimcore-install --admin-username $ADMIN_USERNAME --admin-password $ADMIN_PASSWORD

  EXPOSE 9000
  ```

4. Create a file named `${PROJECT_ROOT}/pimcore/app/config/installer.yml` and copy the following contents into the file:

  _`${PROJECT_ROOT}/pimcore/app/config/installer.yml`_
  ```
  pimcore_install:
      parameters:
          database_credentials:
              user:                 %env(DB_USER)%
              password:             %env(DB_PASSWORD)%
              dbname:               %env(DB_NAME)%
              host:                 %env(DB_HOST)%
              port:                 %env(DB_PORT)%
  ```
  
5. Copy [`wait-for-it.sh`](https://github.com/vishnubob/wait-for-it) in the project root directory to the pimcore directory:

  ```sh
  cp wait-for-it.sh pimcore/
  ```