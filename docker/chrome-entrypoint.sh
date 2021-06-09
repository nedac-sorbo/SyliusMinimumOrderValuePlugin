#!/usr/bin/env bash

set -e

/opt/bin/start-xvfb.sh &
sleep 20
/opt/bin/start-vnc.sh &
sleep 20
google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --remote-debugging-port=9222 --window-size=1360,1020 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
