#!/bin/bash
set +e

# deploy idm
# ./deploy.sh

BASE_DIR=`dirname $0`

echo "Updating dyndns and sleep for 5 seconds"
# curl --insecure https://freedns.afraid.org/dynamic/update.php?N1ROamozNmlTaEgydkM3M3lYNjM6MTU2NTc4OTY=
curl --insecure https://carol.selfhost.de/update?username=303092&password=GegJabCij2&textmodi=1
sleep 10
echo "Transfering data"
rsync -avzh --exclude='/.env' --exclude='/.git' --exclude='/node_modules' * -e "ssh -p 822 -o ConnectTimeout=5" headshot_ftp@kdn10.futureweb.at:/idm.headshot.at/
echo "Rsynced data"

