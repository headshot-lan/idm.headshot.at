#!/bin/bash
set +e

# deploy idm
# ./deploy.sh

BASE_DIR=`dirname $0`

#echo "Updating dyndns and sleep for 5 seconds"
# curl --insecure https://freedns.afraid.org/dynamic/update.php?N1ROamozNmlTaEgydkM3M3lYNjM6MTU2NTc4OTY=
#curl --insecure https://carol.selfhost.de/update?username=303092&password=GegJabCij2&textmodi=1
#sleep 5
echo "Transfering data"
rsync -avzh --exclude-from=".deployignore" --delete * -e "ssh -p 822 -o ConnectTimeout=5" headshot_ftp@kdn10.futureweb.at:/idm.headshot.at/

echo "Rsynced data, clearing cache"
ssh -p 822 headshot_ftp@kdn10.futureweb.at "rm -rf /idm.headshot.at/var/cache/*"
