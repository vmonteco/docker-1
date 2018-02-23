#!/usr/bin/env bash

bash -c "sudo service redis-server restart && sudo service postgresql restart && service nginx start && gitlab-ctl start" | tail
