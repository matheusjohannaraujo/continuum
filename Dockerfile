#FROM php:8.4-apache
#FROM php:8.3-apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html/

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    iputils-ping \
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

# Update packages and install required dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxpm-dev \
    libwebp-dev \
    libavif-dev \
    libbz2-dev \
    libonig-dev \
    libicu-dev \
    libgmp-dev \
    libldap2-dev \
    libzip-dev \
    unzip \
    libpq-dev \
    libssl-dev \
    libxml2-dev \
    libsqlite3-dev \
    zlib1g-dev \
    libcurl4-openssl-dev \
    libedit-dev \
    libpspell-dev \
    libreadline-dev \
    libtidy-dev \
    libxslt1-dev \
    libgmp-dev \
    libldb-dev \
    libaspell-dev \
    libbison-dev \
    libedit-dev \
    libkrb5-dev \
    libpcre3-dev \
    libsnmp-dev \
    libtidy-dev \
    libxslt1-dev \
    libzip-dev \
    && docker-php-source extract \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-source delete

# Activate Apache modules and install PHP extensions
RUN a2enmod rewrite headers \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    intl \
    session \
    mbstring \
    opcache \
    sockets \
    exif \
    pcntl \
    bcmath \
    bz2 \
    iconv \
    && pecl install -o -f redis-5.3.7 \
    #&& pecl install -o -f xdebug-3.2.1 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

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

# https://github.com/docker-library/php/issues/1082
# https://stackoverflow.com/questions/76998840/change-apache-root-folder-on-docker
# https://semaphoreci.com/community/tutorials/dockerizing-a-php-application
COPY ./config/apache-config.conf /etc/apache2/sites-available/000-default.conf

COPY ./www/ .

RUN chmod +x /var/www/phpapache/startup.sh && \
    chmod -R 0777 /var/www/html/ && \
    chown -R $user:$user /var/www/html/ && \
    chmod -R 0777 /var/www/phpapache/ && \
    chown -R $user:$user /var/www/phpapache/ && \
    cd /var/www/html/; /usr/local/bin/composer install --ignore-platform-reqs --no-interaction

#USER $user

CMD ["/var/www/phpapache/startup.sh"]
