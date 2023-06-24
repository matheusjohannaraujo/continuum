#!/bin/bash
/opt/lampp/lampp startapache
/usr/sbin/cron -L 8 &

if [ -f /opt/lampp/htdocs/task ]; then
   tasks=$(cat /opt/lampp/htdocs/task);
   if [ ! -z "$tasks" ]; then
      echo "$tasks" | /usr/bin/crontab -
   else
      echo "" | /usr/bin/crontab -
   fi
fi

/usr/bin/supervisord -n
