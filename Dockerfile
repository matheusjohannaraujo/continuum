FROM tomsik68/xampp:8

RUN apt update && apt install htop cron -y

RUN ln -fs /usr/share/zoneinfo/America/Recife /etc/localtime && \
dpkg-reconfigure -f noninteractive tzdata

WORKDIR /opt/lampp/htdocs

RUN chmod -R 0777 /opt/lampp/htdocs/

RUN ln -s /opt/lampp/bin/php /usr/bin

RUN ln -s /opt/lampp/bin/mysql /usr/bin

RUN ln -s /opt/lampp/bin/pecl /usr/bin

RUN ln -s /opt/lampp/bin/pear /usr/bin

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php composer-setup.php

RUN mv composer.phar /usr/local/bin/composer

RUN rm composer-setup.php

# COPY ./www .

# RUN chmod -R 0777 storage/

# RUN composer update -n

# RUN touch .env

# RUN chmod 0777 .env

# RUN cat .env.example > .env

# RUN echo "APP_URL=http://localhost/" >> .env

RUN echo "/opt/lampp/lampp startapache" > /startup.sh

RUN echo "/usr/bin/supervisord -n" >> /startup.sh

# /opt/lampp/etc/php.ini
# find / -name "redis.so"
# RUN apt install php-redis -y
# session.save_handler=files
# session.save_handler=redis
#RUN sed -i 's/session\.save_handler=files/session.save_handler=redis/g' /opt/lampp/etc/php.ini
# session.save_path="/opt/lampp/temp/"
# session.save_path="tcp://127.0.0.1:6379?auth=password"
#RUN sed -i 's#session\.save_path="/opt/lampp/temp/"#session.save_path="tcp://127.0.0.1:6379"#g' /opt/lampp/etc/php.ini
#RUN echo "extension=redis.so" >> /opt/lampp/etc/php.ini

RUN chmod +x /startup.sh

RUN /opt/lampp/lampp stop

RUN /opt/lampp/lampp startapache
