#!/usr/bin/env bash

for i in $(seq 1 10)
do
  sleep 0.5
  echo "Centering wallpaper"
  /usr/bin/fbsetbg -c /usr/share/images/fluxbox/ubuntu-light.png
  if [ $? -eq 0 ]; then
    break
  fi
done

#if [ ! -z $VNC_NO_PASSWORD ]; then
    echo "Starting VNC server without password authentication"
    X11VNC_OPTS=
#else
#    X11VNC_OPTS=-usepw
#fi

for i in $(seq 1 10)
do
  sleep 1
  xdpyinfo -display ${DISPLAY} >/dev/null 2>&1
  if [ $? -eq 0 ]; then
    break
  fi
  echo "Waiting for Xvfb..."
done

x11vnc ${X11VNC_OPTS} -forever -shared -rfbport ${VNC_PORT:-5900} -rfbportv6 ${VNC_PORT:-5900} -display ${DISPLAY}
