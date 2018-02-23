#!/usr/bin/env bash

#
#   INIT DB
#

sudo -u postgres service postgresql start
sudo -u postgres psql -d template1 -c "CREATE USER git CREATEDB;"
sudo -u postgres psql -d template1 -c "CREATE EXTENSION IF NOT EXISTS pg_trgm;"
sudo -u postgres psql -d template1 -c "CREATE DATABASE gitlabhq_production OWNER git;"

#
#   REDIS START
#

sudo service redis-server restart

#
#   BUNDLE INSTALL
#

#sudo -u git -H bundle install --deployment --without development test mysql aws kerberos

# Steps to add :
# 1- Update DB conf (config/database.yml)
#    - database name
#    - username
#    - password # REQUIRED
#    - host     # REQUIRED
# 2- Update redis server conf
# 3- Update host in gitlab.yml
# 4- Update email_from in gitlab.yml (use host value)
# 5- Update email_reply_to
# 6- Install gitlab-shell (export `RUBYOPT=--disable-gems` and compile ruby with `configure --disable-rubygems`)

bash
