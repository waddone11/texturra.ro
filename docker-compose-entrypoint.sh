#!/bin/bash

/sbin/runuser -u www-data -- php artisan migrate

/usr/local/bin/apache2-foreground
