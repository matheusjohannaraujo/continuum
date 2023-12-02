#!/bin/bash
/usr/sbin/cron -L 8 &

#if [ $UID -eq 0 ]; then
#  # allocate swap space
#  fallocate -l 512M /swapfile
#  chmod 0600 /swapfile
#  mkswap /swapfile
#  echo 10 > /proc/sys/vm/swappiness
#  swapon /swapfile
#  echo 1 > /proc/sys/vm/overcommit_memory
#fi

composer install \
   --ignore-platform-reqs \
   --no-interaction

if [ ! -f ".env" ]; then
   cp .env.example .env
   echo "APP_URL=http://localhost/" >> .env
fi

if [ -f /var/www/phpapache/task.cron ]; then
   tasks=$(cat /var/www/phpapache/task.cron);
   if [ ! -z "$tasks" ]; then
      echo "$tasks" | /usr/bin/crontab -
   else
      echo "" | /usr/bin/crontab -
   fi
fi

/usr/bin/supervisord -n
