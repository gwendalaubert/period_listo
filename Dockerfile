FROM php:8.1-cli as BASE

# Install dependencies
RUN apt-get update \
  && apt-get install -yqq \
  libzip-dev \
  git \
  wget \
  bash \
  --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Symfony
RUN wget --progress=dot:giga https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy in custom code from the host machine.
WORKDIR /sources

COPY composer.json composer.lock ./

RUN composer install --no-progress --no-interaction --no-scripts

COPY . /sources

RUN composer dump-autoload --optimize


FROM php:8.1.3-apache as DEV

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://github.com/docker-library/docs/blob/master/php/README.md#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN a2enmod rewrite

# Symfony conf
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY --from=BASE --chown=www-data:www-data /sources /var/www/html