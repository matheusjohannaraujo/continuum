FROM tomsik68/xampp:8

WORKDIR /opt/lampp/htdocs

RUN apt update && apt install -y supervisor cron htop && \
    ln -fs /usr/share/zoneinfo/America/Recife /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    chmod -R 0777 /opt/lampp/htdocs/ && \
    ln -s /opt/lampp/bin/php /usr/bin && \
    ln -s /opt/lampp/bin/mysql /usr/bin && \
    ln -s /opt/lampp/bin/pecl /usr/bin && \
    ln -s /opt/lampp/bin/pear /usr/bin && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    mv composer.phar /usr/local/bin/composer && \
    rm composer-setup.php

COPY ./www .

COPY ./task /opt/lampp/htdocs/task

COPY ./startup.sh /startup.sh

ADD supervisord.conf /etc/supervisor/conf.d/

RUN touch .env && \
    chmod 0777 .env && \
    cat .env.example > .env && \
    echo "APP_URL=http://localhost/" >> .env && \
    chmod 0777 /opt/lampp/htdocs/task && \
    chmod +x /startup.sh && \
    chmod -R 0777 storage/ && \
    composer update -n && \
    /opt/lampp/lampp stop && \
    /opt/lampp/lampp startapache
