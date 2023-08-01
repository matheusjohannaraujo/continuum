#FROM php:8.2.8-apache
#FROM php:8.1.21-apache
FROM php:8.0.29-apache
#FROM php:7.4.33-apache
#FROM php:7.3.33-apache
#FROM php:7.2.34-apache
#FROM php:7.1.33-apache
#FROM php:7.0.33-apache

# Set working directory
WORKDIR /var/www/html/

# Set args
ARG user=continuum
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libbz2-dev \
    zip \
    unzip \
    htop \
    supervisor \
    cron && \
    ln -fs /usr/share/zoneinfo/America/Recife /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets bz2 intl mysqli pdo pdo_pgsql iconv session opcache && \
    a2enmod rewrite

# Install redis
RUN pecl install -o -f redis-5.3.7 \
#    && pecl install -o -f xdebug-3.2.1 \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis
# xdebug

# Install Composer
RUN chmod -R 0777 /var/www/html/ && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    mv composer.phar /usr/local/bin/composer && \
    rm composer-setup.php && \
    composer config -g repo.packagist composer https://packagist.org

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

COPY ./www .

RUN touch .env && \
    chmod 0777 .env && \
    cat .env.example > .env && \
    echo "APP_URL=http://localhost/" >> .env && \
    composer update -n && \
    chmod -R 0777 storage/

# Copy custom configurations PHP
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

ADD supervisord.conf /etc/supervisor/conf.d/

USER $user
