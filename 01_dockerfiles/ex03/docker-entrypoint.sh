#!/usr/bin/env bash

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
