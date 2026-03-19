#!/bin/bash
# Watchdog: スクレイパーが死んだら --resume で再起動
SCRAPER_DIR="$(dirname "$0")"
LOG_FILE="$SCRAPER_DIR/../../data/watchdog.log"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Watchdog started" >> "$LOG_FILE"

while true; do
    # Check if any scraper is running
    if ! pgrep -f "hojyokin-portal.py" > /dev/null 2>&1; then
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] Scraper not running, restarting with --resume" >> "$LOG_FILE"
        cd "$SCRAPER_DIR/../.."
        nohup python3 scripts/scraper/hojyokin-portal.py --resume >> data/scraper-restart.log 2>&1 &
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] Restarted PID: $!" >> "$LOG_FILE"
        sleep 30  # Wait for it to start
    fi
    
    # Check every 2 minutes
    sleep 120
done
