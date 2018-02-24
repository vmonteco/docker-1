#!/usr/bin/env bash

function entrypoint() {
	if [ -z ${SSH_PORT} ]
	then
	   sed -i "/    # ssh_port: 22/c\    ssh_port: $SSH_PORT" /home/git/gitlab/config/gitlab.yml
	fi
	if [ -z ${GITLAB_HOST} ]
	then
	   sed -i "/    host: localhost/c\    host: $GITLAB_HOST" /home/git/gitlab/config/gitlab.yml
	fi
	service gitlab start &
	service redis-server restart
	service postgresql restart
	service ssh restart
	nginx -g "daemon off;" | tail
}

if [ $# -eq 0 ]
then
	entrypoint
else
	bash $#
fi

