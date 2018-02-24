FROM debian

#
#   1 - PACKAGES /DEPENDENCIES
#

RUN apt-get update -y \
	&& apt-get upgrade -y

RUN apt-get install -y sudo \
	build-essential zlib1g-dev libyaml-dev libssl-dev libgdbm-dev libre2-dev \
	libreadline-dev libncurses5-dev libffi-dev curl openssh-server checkinstall \
	libxml2-dev libxslt-dev libcurl4-openssl-dev libicu-dev logrotate rsync \
	python-docutils pkg-config cmake

#
#   FOR DEV PURPOSE
#

RUN apt-get install -y emacs

#
#   ENSURE THAT WE HAVE THE RIGHT GIT VERSION
#

RUN apt-get install -y git-core

# RUN apt-get remove -y git-core \
# 	&& apt-get install -y libexpat1-dev gettext libz-dev \
# 	&& cd /tmp \
# 	&& curl --remote-name --progress https://www.kernel.org/pub/software/scm/git/git-2.14.3.tar.gz \
# 	&& echo '023ffff6d3ba8a1bea779dfecc0ed0bb4ad68ab8601d14435dd8c08416f78d7f  git-2.14.3.tar.gz' | shasum -a256 -c - && tar -xzf git-2.14.3.tar.gz \
# 	&& cd git-2.14.3/ \
# 	&& ./configure \
# 	&& make prefix=/usr/local all \
# 	&& make prefix=/usr/local install

#
#   INSTALL POSTFIX
#

RUN bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\"" \
	&& bash -c "debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\"" \
	&& apt-get install -y postfix

#
#   2 - INSTALL RUBY
#


# 2.4.3 sans le sha

RUN apt-get remove -y ruby1.8 \
	&& mkdir /tmp/ruby && cd /tmp/ruby \
	&& curl --remote-name --progress https://cache.ruby-lang.org/pub/ruby/2.4/ruby-2.4.3.tar.gz \
	&& tar xvfz ruby-2.4.3.tar.gz \
	&& cd ruby-2.4.3 \
	&& ./configure --disable-install-rdoc \
	&& make \
	&& make install \
	&& gem install bundler --no-ri --no-rdoc

# RUN mkdir /tmp/ruby && cd /tmp/ruby \
# 	&& sudo apt-get -y install zlib1g-dev libssl-dev libreadline-dev libgdbm-dev openssl wget \
# 	&& wget http://cache.ruby-lang.org/pub/ruby/2.4/ruby-2.4.3.tar.gz \
# 	&& tar xvfz ruby-2.4.3.tar.gz \
# 	&& cd ruby-2.4.3 \
# 	&& ./configure \
# 	&& make \
# 	&& sudo make install \
# 	&& gem install bundler --no-ri --no-rdoc

#
#   3 - INSTALL GO
#

RUN rm -rf /usr/local/go \
	&& cd /tmp && curl --remote-name --progress https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz \
	&& echo '1862f4c3d3907e59b04a757cfda0ea7aa9ef39274af99a784f5be843c80c6772  go1.8.3.linux-amd64.tar.gz' | shasum -a256 -c - \
	&& tar -C /usr/local -xzf go1.8.3.linux-amd64.tar.gz \
	&& ln -sf /usr/local/go/bin/go /usr/local/bin/ \
	&& ln -sf /usr/local/go/bin/godoc /usr/local/bin/ \
	&& ln -sf /usr/local/go/bin/gofmt /usr/local/bin/ \
  	&& rm go1.8.3.linux-amd64.tar.gz

#
#   4 - INSTALL NODE
#


RUN curl --location https://deb.nodesource.com/setup_8.x | sudo bash - \
	&& sudo apt-get install -y nodejs \
	&& curl --silent --show-error https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - \
	&& echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list \
	&& sudo apt-get update -y \
	&& sudo apt-get install -y yarn


#
#   5 - CREATE GIT USER
#

RUN adduser --disabled-login --gecos 'GitLab' git

#
#   6 - DATABASE
#


# Ajout d'un mdp

RUN apt-get install -y postgresql postgresql-client libpq-dev postgresql-contrib \
	&& service postgresql start \
	&& sudo -u postgres psql -d template1 -c "CREATE USER git CREATEDB;" \
	&& sudo -u postgres psql -d template1 -c "CREATE EXTENSION IF NOT EXISTS pg_trgm;" \
		&& sudo -u postgres psql -d template1 -c "ALTER USER \"git\" WITH PASSWORD 'password';" \
	&& sudo -u postgres psql -d template1 -c "CREATE DATABASE gitlabhq_production OWNER git;"



#
#   7 - REDIS INSTALLATION
#

RUN sudo apt-get install -y redis-server \
	&& sudo cp /etc/redis/redis.conf /etc/redis/redis.conf.orig \
	&& sed 's/^port .*/port 0/' /etc/redis/redis.conf.orig | sudo tee /etc/redis/redis.conf \
	&& echo 'unixsocket /var/run/redis/redis.sock' | sudo tee -a /etc/redis/redis.conf \
	&& echo 'unixsocketperm 770' | sudo tee -a /etc/redis/redis.conf \
	&& mkdir /var/run/redis \
	&& chown redis:redis /var/run/redis \
	&& chmod 755 /var/run/redis \
	&& if [ -d /etc/tmpfiles.d ]; then echo 'd  /var/run/redis  0755  redis  redis  10d  -' | sudo tee -a /etc/tmpfiles.d/redis.conf; fi \
	&& sudo usermod -aG redis git \
	&& sudo service redis-server start

#
#   8 - INSTALL GITLAB
#

WORKDIR /home/git

#   CLONE THE SOURCE

RUN sudo -u git -H git clone https://gitlab.com/gitlab-org/gitlab-ce.git -b 10-5-stable gitlab

#   CONFIGURE GITLAB

WORKDIR /home/git/gitlab

#	&& sed -i '/    bin_path: \/usr\/bin\/git/c\    bin_path: \/usr\/local\/bin\/git/' config/gitlab.yml \

RUN sudo -u git -H cp config/gitlab.yml.example config/gitlab.yml \
	&& sed -i '/    email_reply_to: noreply@example.com/c\    email_reply_to: noreply@localhost/' config/gitlab.yml \
	&& sudo -u git -H cp config/secrets.yml.example config/secrets.yml \
	&& sudo -u git -H chmod 0600 config/secrets.yml \
	&& sudo chown -R git log/ \
	&& sudo chown -R git tmp/ \
	&& sudo chmod -R u+rwX,go-w log/ \
	&& sudo chmod -R u+rwX tmp/ \
	&& sudo chmod -R u+rwX tmp/pids/ \
	&& sudo chmod -R u+rwX tmp/sockets/ \
	&& sudo -u git -H mkdir public/uploads/ \
	&& sudo chmod 0700 public/uploads \
	&& sudo chmod -R u+rwX builds/ \
	&& sudo chmod -R u+rwX shared/artifacts/ \
	&& sudo chmod -R ug+rwX shared/pages/ \
	&& sudo -u git -H cp config/unicorn.rb.example config/unicorn.rb \
	&& sudo -u git -H cp config/initializers/rack_attack.rb.example config/initializers/rack_attack.rb \
	&& sudo -u git -H git config --global core.autocrlf input \
	&& sudo -u git -H git config --global gc.auto 0 \
	&& sudo -u git -H git config --global repack.writeBitmaps true \
	&& sudo -u git -H git config --global receive.advertisePushOptions true \
	&& sudo -u git -H cp config/resque.yml.example config/resque.yml \
	&& sudo -u git cp config/database.yml.postgresql config/database.yml \
	&& sudo -u git -H chmod o-rwx config/database.yml

#&& sed -i '/  url: unix:\/var\/run\/redis\/redis.sock/c\  url: redis:\/\/redis:6379/' config/resque.yml \

#
#   CONFIGURE GITLAB DB SETTINGS
#

RUN sudo -u git cp config/database.yml.postgresql config/database.yml \
	&& sudo -u git -H chmod o-rwx config/database.yml \
	&& sed -i '/  password: "secure password"/c\  password: "password"' config/database.yml \
	&& cat -e config/database.yml

#
#   INSTALL GEMS WITH BUNDLE
#

RUN sudo -u git -H bundle install --deployment --without development test mysql aws kerberos

#
#   INSTALL GITLAB SHELL
#

RUN sudo -u git -H bundle exec rake gitlab:shell:install REDIS_URL=unix:/var/run/redis/redis.sock RAILS_ENV=production SKIP_STORAGE_VALIDATION=true

#
#   INSTALL GITLAB-WORKHORSE
#

RUN sudo -u git -H bundle exec rake "gitlab:workhorse:install[/home/git/gitlab-workhorse]" RAILS_ENV=production


#
#   INITIALIZE DB AND ACTIVATE ADVANCED FEATURES
#

RUN service postgresql restart && service redis-server restart && yes yes | sudo -u git -H bundle exec rake gitlab:setup RAILS_ENV=production

#
#   INSTALL INIT SCRIPT
#

RUN cp lib/support/init.d/gitlab /etc/init.d/gitlab \
	&& update-rc.d gitlab defaults 21

#
#   INSTALL GITALY
#

RUN sudo -u git -H bundle exec rake "gitlab:gitaly:install[/home/git/gitaly]" RAILS_ENV=production

RUN chmod 0700 /home/git/gitlab/tmp/sockets/private \
	&& chown git /home/git/gitlab/tmp/sockets/private

COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

#
#   SETUP LOGROTATE
#

RUN cp lib/support/logrotate/gitlab /etc/logrotate.d/gitlab

#
#   CHECK APPLICATION STATUS
#

RUN service postgresql restart && sudo -u git -H bundle exec rake gitlab:env:info RAILS_ENV=production

#
#   COMPILE GETTEXT PO FILES
#

RUN sudo -u git -H bundle exec rake gettext:compile RAILS_ENV=production

#
#   COMPIEL ASSETS
#

RUN sudo -u git -H yarn install --production --pure-lockfile \
	&& sudo -u git -H bundle exec rake gitlab:assets:compile RAILS_ENV=production NODE_ENV=production

#
#   START GITLAB INSTANCE
#

RUN sudo service gitlab start

#
#   NGINX
#

COPY gitlab /etc/nginx/sites-available/gitlab

#&& cp lib/support/nginx/gitlab /etc/nginx/sites-available/gitlab \

RUN apt-get install -y nginx \	
	&& ln -s /etc/nginx/sites-available/gitlab /etc/nginx/sites-enabled/gitlab \
	&& rm -f /etc/nginx/sites-enabled/default 

EXPOSE 80 22

VOLUME /var/lib/postgresql/9.5/main

#ENTRYPOINT bash
ENTRYPOINT /docker-entrypoint.sh

#CMD ["bash", "b/docker-entrypoint.sh"]
#CMD ["service", "redis-server", "restart", "&&", "service", "postgresql", "restart", "&&", "service", "gitlab", "restart", "&&", "nginx", "-g", "daemon", "off;"]
