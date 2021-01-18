# Start logger
service rsyslog start
# Start cron service
service cron start
# Export required env variables to a file
echo export CONFIG_FILE=$CONFIG_FILE > $HOME/.cron_profile
# Run apache in the foreground
apache2-foreground
