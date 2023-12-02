FROM php:8.3.0-apache
#FROM php:8.2.8-apache
#FROM php:8.1.21-apache
#FROM php:8.0.29-apache
#FROM php:7.4.33-apache
#FROM php:7.3.33-apache

# Set working directory
WORKDIR /var/www/html/

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    git \
    wget \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libbz2-dev \
    zip \
    unzip \
    htop \
    nano \
    supervisor \
    cron && \
    ln -fs /usr/share/zoneinfo/America/Recife /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    apt-get clean && \
    apt-get autoclean && \
    apt-get autoremove && \
    rm -rf /var/lib/apt/lists/*

# Configure and Install PHP extensions
RUN a2enmod rewrite && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets bz2 intl mysqli pdo pdo_pgsql iconv session opcache && \
    pecl install -o -f redis-5.3.7 && \
#    pecl install -o -f xdebug-3.2.1 && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis

# Install Composer
RUN chmod -R 0777 /var/www/html/ && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    mv composer.phar /usr/local/bin/composer && \
    rm composer-setup.php && \
    composer config -g repo.packagist composer https://packagist.org

# Set args
ARG user=phpapache
ARG uid=1000

# Create system user to run Composer
RUN useradd -G www-data,root -u $uid -d /home/$user $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    mkdir -p /var/www/phpapache/ && \
    chmod -R 0777 /var/www/phpapache/

COPY ./config/task.cron /var/www/phpapache/task.cron

COPY ./config/startup.sh /var/www/phpapache/startup.sh

COPY ./config/php.ini /usr/local/etc/php/conf.d/custom.ini

COPY ./config/supervisord.conf /etc/supervisor/conf.d/

RUN chmod +x /var/www/phpapache/startup.sh && \
    chmod -R 0777 /var/www/html/ && \
    chown -R $user:$user /var/www/html/ && \
    chmod -R 0777 /var/www/phpapache/ && \
    chown -R $user:$user /var/www/phpapache/

#USER $user

CMD ["/var/www/phpapache/startup.sh"]
