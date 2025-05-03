#!/bin/bash

# Determine the absolute path to the cron.php file
CRON_FILE_PATH=$(realpath $(dirname $0)/cron.php)

# Check if the cron job already exists (to avoid duplicates)
if ! crontab -l | grep -q "$CRON_FILE_PATH"; then
    # Add the cron job to run every 24 hours (at 00:00)
    (crontab -l ; echo "0 0 * * * php $CRON_FILE_PATH") | crontab -

    echo "CRON job added to run cron.php daily at 00:00."
else
    echo "CRON job for cron.php already exists."
fi

echo "Remember to ensure your system's CRON service is running."