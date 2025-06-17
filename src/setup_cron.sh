#!/bin/bash

CRON_JOB="0 9 * * * php $(pwd)/cron.php"
(crontab -l ; echo "$CRON_JOB") | sort -u | crontab -
echo "CRON job added to send XKCD comic daily at 9 AM."
