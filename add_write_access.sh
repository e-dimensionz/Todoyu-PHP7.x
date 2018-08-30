#!/bin/bash

mkdir files
mkdir log 
mkdir cache 
chown www-data:www-data files
chown www-data:www-data config
chown www-data:www-data cache 
chown www-data:www-data log
chown www-data:www-data install/config
chown www-data:www-data config/db.php
chown www-data:www-data config/extensions.php
chown www-data:www-data config/extconf.php
chown www-data:www-data config/system.php
chown www-data:www-data index.html

