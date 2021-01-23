#!/bin/sh

if [ ! $1 ]
then
    echo "ERROR: Must provide hive-mq container name"
    exit 1
fi

CONTAINER_NAME=$1

if [ ! $2 ]
then
    echo "ERROR: Must specify a password to hash"
    exit 1
fi

CLEAR_PASSWORD=$2

docker exec $CONTAINER_NAME java -jar ./extensions/hivemq-file-rbac-extension/hivemq-file-rbac-extension-4.4.0.jar -p $CLEAR_PASSWORD
