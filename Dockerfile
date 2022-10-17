FROM fehren/php-apache:8.0.23

WORKDIR /var/www/html

COPY ./www .

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php composer-setup.php

RUN mv composer.phar /usr/local/bin/composer

RUN rm composer-setup.php

RUN composer update
