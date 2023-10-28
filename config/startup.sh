#!/bin/bash
/usr/sbin/cron -L 8 &

# allocate swap space
fallocate -l 512M /swapfile
chmod 0600 /swapfile
mkswap /swapfile
echo 10 > /proc/sys/vm/swappiness
swapon /swapfile
echo 1 > /proc/sys/vm/overcommit_memory

if [ -f /var/www/html/task.cron ]; then
   tasks=$(cat /var/www/html/task.cron);
   if [ ! -z "$tasks" ]; then
      echo "$tasks" | /usr/bin/crontab -
   else
      echo "" | /usr/bin/crontab -
   fi
fi

/usr/bin/supervisord -n
