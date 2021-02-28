#!/bin/sh
# Start logger
sudo service rsyslog start
# Start cron service
sudo service cron start
# Export required env variables to a file
echo export CONFIG_FILE=$CONFIG_FILE > $HOME/.cron_profile
# Run apache in the foreground
sudo apache2-foreground
