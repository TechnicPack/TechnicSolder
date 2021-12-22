#!/bin/bash

printenv | grep "ENV_" > /var/www/html/.env
echo "\n" >> /var/www/html/.env
printenv | grep "SOLDER_" >> /var/www/html/.env

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf