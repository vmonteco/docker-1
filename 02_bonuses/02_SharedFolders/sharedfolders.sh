#!/usr/bin/env sh


# /!\ /!\ You may want to run #11 first in order to compare /!\ /!\

MACHINE_NAME=Char
VOLUME_NAME=hatchery_bis
VOLUME_HOST_PATH=$(pwd)/hatchery_bis
VOLUME_GUEST_PATH=/hatchery_bis

if [[ $(docker-machine status $MACHINE_NAME ) == "Running" ]]; then
   echo "Stopping $MACHINE_NAME docker machine.";
   docker-machine stop $MACHINE_NAME;
   echo "$MACHINE_NAME docker machine stopped.";
fi

mkdir $VOLUME_HOST_PATH

echo "Adding volume to VM :"
VBoxManage sharedfolder add $MACHINE_NAME --name $VOLUME_NAME --hostpath $VOLUME_HOST_PATH

echo "Restarting machine :"
docker-machine start $MACHINE_NAME

echo "Mounting volume in docker machine :"
docker-machine ssh $MACHINE_NAME "sudo mkdir $VOLUME_GUEST_PATH; sudo mount -t vboxsf -o uid=999,gid=50 $VOLUME_NAME $VOLUME_GUEST_PATH"
echo "Done."

