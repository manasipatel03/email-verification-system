#!/bin/bash

# Get absolute path to cron.php
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
CRON_PATH="$SCRIPT_DIR/cron.php"

# Add the CRON job to run every 5 minutes
(crontab -l 2>/dev/null; echo "*/5 * * * * /usr/bin/php $CRON_PATH") | crontab -

echo "CRON job has been set up to run every 5 minutes."
echo "Path: $CRON_PATH"