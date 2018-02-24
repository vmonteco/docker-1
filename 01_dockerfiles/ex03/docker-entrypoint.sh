#!/usr/bin/env bash

function entrypoint() {
	sed -i "/    # ssh_port: 22/c\    ssh_port: ${SSH_PORT:-22}" /home/git/gitlab/config/gitlab.yml
	sed -i "/    host: localhost/c\    host: ${GITLAB_HOST:-localhost}" /home/git/gitlab/config/gitlab.yml
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

