#!/bin/bash

pkill -f "^gammu"
usb_modeswitch -v 12d1 -p 1001 -H -I -W
bash /srv/gammu/reset.sh
sleep 3
service gammu-smsd restart
