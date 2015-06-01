#!/bin/bash

cp config/autoload/local.php.dist config/autoload/local.php;
cp tests/directors.ini /etc/bareos-webui/directors.ini

exit 0;
