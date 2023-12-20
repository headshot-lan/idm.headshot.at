#!/bin/bash
set +e

# deploy idm
# ./deploy.sh

BASE_DIR=`dirname $0`


rsync -avzh --exclude='/.env' --exclude='/.git' --exclude='/node_modules' * -e "ssh -p 822" headshot_ftp@idm.headshot.at:/idm.headshot.at/
echo "Rsynced data"

