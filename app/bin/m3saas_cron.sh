#!/bin/sh

FILE="$HOME/.cron_profile"
if test -f $FILE; then
    . $HOME/.cron_profile
fi

output=`CRON_JOBS=true /usr/local/bin/php -f /var/www/html/index.php`
if [ $? -eq 0 ]; then
    logger OK
    echo OK
else
    logger FAIL with code $?
    echo FAIL with code $?

    logger output: $output
    echo output: $output
fi

exit 0
