#!/bin/bash

echo "Start cron"
/usr/sbin/cron -L 8 &

echo "Start composer install"
/usr/local/bin/composer install \
    --ignore-platform-reqs \
    --no-interaction

echo "Config env"
if [ ! -f ".env" ]; then
   cp .env.example .env
   echo "APP_URL=http://localhost/" >> .env
fi

#echo "Config swap"
#if [ $UID -eq 0 ]; then
#  # allocate swap space
#  fallocate -l 512M /swapfile
#  chmod 0600 /swapfile
#  mkswap /swapfile
#  echo 10 > /proc/sys/vm/swappiness
#  swapon /swapfile
#  echo 1 > /proc/sys/vm/overcommit_memory
#fi

echo "Config cron"
if [ -f /var/www/phpapache/task.cron ]; then
   tasks=$(cat /var/www/phpapache/task.cron);
   if [ ! -z "$tasks" ]; then
      echo "$tasks" | /usr/bin/crontab -
   else
      echo "" | /usr/bin/crontab -
   fi
fi

echo "Start supervisord"
/usr/bin/supervisord -n &

echo "Start apache"
/usr/local/bin/apache2-foreground
