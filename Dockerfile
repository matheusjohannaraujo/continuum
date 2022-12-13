FROM tomsik68/xampp:8

WORKDIR /opt/lampp/htdocs

COPY ./www .

RUN ln -s /opt/lampp/bin/php /usr/bin

RUN ln -s /opt/lampp/bin/mysql /usr/bin

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php composer-setup.php

RUN mv composer.phar /usr/local/bin/composer

RUN rm composer-setup.php

RUN chmod -R 0777 storage/

RUN chmod -R 0777 /opt/lampp/htdocs/

RUN composer update -n

RUN touch .env

RUN chmod 0777 .env

RUN cat .env.example > .env

# RUN echo "APP_URL=http://localhost/" >> .env

RUN echo "/opt/lampp/lampp startapache" > /startup.sh

RUN echo "/usr/bin/supervisord -n" >> /startup.sh

RUN chmod +x /startup.sh

RUN /opt/lampp/lampp stop

RUN /opt/lampp/lampp startapache

RUN apt update && apt install htop -y
