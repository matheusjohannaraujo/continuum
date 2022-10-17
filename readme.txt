# Install Docker

curl -fsSL https://get.docker.com -o get-docker.sh

sh get-docker.sh

# Commands Docker

docker-compose down --remove-orphans

docker-compose up -d --build

docker exec -it continuum-web-1 bash

# Example of configuration web container

ln -s /opt/lampp/bin/php /usr/bin/

ln -s /opt/lampp/bin/mysql /usr/bin/

cd /opt/lampp/htdocs

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php composer-setup.php

mv composer.phar /usr/local/bin/composer

rm composer-setup.php

composer update
