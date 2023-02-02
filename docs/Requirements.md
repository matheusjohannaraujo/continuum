### Requirements

* EN-US: **Information about the settings that must exist for the project to work correctly**
* 
* * Requires the minimum version of PHP 7.2.5
* * You need to have the "composer" installed
* * With "composer", run the following command
* * * **```composer update --ignore-platform-reqs```**
* 
* * Install Docker
* * * **```curl -fsSL https://get.docker.com -o get-docker.sh```**
* * * **```sh get-docker.sh```**
* 
* * Running the install through Docker - Apache, PHP, MySQL and Composer (run the commands in the folder that has the `Dockerfile` and `docker-compose.yml` files)
* * * **```docker-compose down --remove-orphans```**
* * * **```docker-compose up -d --build```**
* * * **```docker-compose ps```**
* * * **```docker exec -it continuum-web-1 bash```**
* * * **```composer update```**
* 
* * Example of configuration web container Docker
* * * **```ln -s /opt/lampp/bin/php /usr/bin/```**
* * * **```ln -s /opt/lampp/bin/mysql /usr/bin/```**
* * * **```cd /opt/lampp/htdocs```**
* * * **```php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"```**
* * * **```php composer-setup.php```**
* * * **```mv composer.phar /usr/local/bin/composer```**
* * * **```rm composer-setup.php```**
* * * **```composer update```**
* 
* * Installers for Linux Debian - Apache, PHP, MySQL and Composer
* * * **```sudo apt update```**
* * * **```sudo apt install -y lsb-release apt-transport-https ca-certificates wget curl```**
* * * **```sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg```**
* * * **```sudo echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list```**
* * * **```sudo apt update```**
* * * **```sudo apt install -y software-properties-common git unzip apache2 mysql-server php7.4-{cli,curl,bcmath,memcached,dev,bz2,intl,pgsql,sqlite3,xmlrpc,xml,gd,json,mbstring,mysql,zip,soap,intl,readline} libapache2-mod-php7.4 phpmyadmin composer```**
* * * **```composer config -g repo.packagist composer https://packagist.phpcomposer.com```**
* * * **```composer config -g repo.packagist composer https://packagist.org```**
* * * **```composer config -g github-protocols https ssh```**
* * * <a href="https://askubuntu.com/questions/421233/enabling-htaccess-file-to-rewrite-path-not-working">It is necessary to have enabled the reading of htaccess files</a>
* 
* * Installers for Windows - Apache, PHP, MySQL and Composer
* * * <a href="https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.4.3/xampp-windows-x64-7.4.3-0-VC15-installer.exe/download">Download: xampp-windows-x64-7.4.3-0-VC15-installer.exe</a>
* * * <a href="https://getcomposer.org/Composer-Setup.exe">Download: Composer-Setup.exe</a>

### [Back to the previous page](./DOC-EU.md)

<hr>

### Exigências

* PT-BR: **Informações sobre as configurações que devem existir para que o projeto funcione corretamente**
* 
* * Requer a versão mínima do PHP 7.2.5
* * Você precisa ter o "composer" instalado
* * Com "composer", execute o seguinte comando
* * * **```composer update --ignore-platform-reqs```**
* 
* * Instalar Docker
* * * **```curl -fsSL https://get.docker.com -o get-docker.sh```**
* * * **```sh get-docker.sh```**
* 
* * Executando a instalação pelo Docker - Apache, PHP, MySQL and Composer (run the commands in the folder that has the `Dockerfile` and `docker-compose.yml` files)
* * * **```docker-compose down --remove-orphans```**
* * * **```docker-compose up -d --build```**
* * * **```docker exec -it continuum-web-1 bash```**
* 
* * Exemplo de configuração do Docker container web
* * * **```ln -s /opt/lampp/bin/php /usr/bin/```**
* * * **```ln -s /opt/lampp/bin/mysql /usr/bin/```**
* * * **```cd /opt/lampp/htdocs```**
* * * **```php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"```**
* * * **```php composer-setup.php```**
* * * **```mv composer.phar /usr/local/bin/composer```**
* * * **```rm composer-setup.php```**
* * * **```composer update```**
* 
* * Instaladores para Linux Debian - Apache, PHP, MySQL e Composer
* * * **```sudo apt update```**
* * * **```sudo apt install -y lsb-release apt-transport-https ca-certificates wget curl```**
* * * **```sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg```**
* * * **```sudo echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list```**
* * * **```sudo apt update```**
* * * **```sudo apt install -y software-properties-common git unzip apache2 mysql-server php7.4-{cli,curl,bcmath,memcached,dev,bz2,intl,pgsql,sqlite3,xmlrpc,xml,gd,json,mbstring,mysql,zip,soap,intl,readline} libapache2-mod-php7.4 phpmyadmin composer```**
* * * **```composer config -g repo.packagist composer https://packagist.phpcomposer.com```**
* * * **```composer config -g repo.packagist composer https://packagist.org```**
* * * **```composer config -g github-protocols https ssh```**
* * * <a href="https://askubuntu.com/questions/421233/enabling-htaccess-file-to-rewrite-path-not-working">É necessário ter habilitado a leitura dos arquivos htaccess</a>
* 
* * Instaladores para Windows - Apache, PHP, MySQL e Composer
* * * <a href="https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.4.3/xampp-windows-x64-7.4.3-0-VC15-installer.exe/download">Baixar: xampp-windows-x64-7.4.3-0-VC15-installer.exe</a>
* * * <a href="https://getcomposer.org/Composer-Setup.exe">Baixar: Composer-Setup.exe</a>

### [Voltar para página anterior](./DOC.md)
