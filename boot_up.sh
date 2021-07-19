#!/bin/bash
hostname=`hostname`
if [[ $hostname == *-0 ]];then
    nodeNum="0"
else
    nodeNum="other"
fi
if [ $nodeNum == "0" ]; then
    cd /var/www
    # kill old process
    sudo -u www-data php artisan horizon:terminate
    # start new process
    nohup sudo -u www-data php artisan horizon &
else
    echo "not node 0"
fi
