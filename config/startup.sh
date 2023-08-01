#!/bin/bash
/usr/sbin/cron -L 8 &

if [ -f /var/www/html/task.cron ]; then
   tasks=$(cat /var/www/html/task.cron);
   if [ ! -z "$tasks" ]; then
      echo "$tasks" | /usr/bin/crontab -
   else
      echo "" | /usr/bin/crontab -
   fi
fi

/usr/bin/supervisord -n
