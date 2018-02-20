FROM ubuntu

RUN apt-get update -y \
	&& apt-get upgrade -y \
	&& apt-get install -y sudo curl ca-certificates build-essential \
	zlib1g-dev libyaml-dev libssl-dev libgdbm-dev libre2-dev libreadline-dev \
	libncurses5-dev libffi-dev curl openssh-server checkinstall libxml2-dev \
	libxslt-dev libcurl4-openssl-dev libicu-dev logrotate rsync \
	python-docutils pkg-config cmake libexpat1-dev gettext libz-dev libssl-dev \
	libmysqlclient-dev emacs apt-utils \
	&& sudo apt-get remove -y git-core \
	&& cd /tmp \
	&& curl --remote-name --progress https://www.kernel.org/pub/software/scm/git/git-2.14.3.tar.gz \
	&& echo '023ffff6d3ba8a1bea779dfecc0ed0bb4ad68ab8601d14435dd8c08416f78d7f  git-2.14.3.tar.gz' | shasum -a256 -c - && tar -xzf git-2.14.3.tar.gz \
	&& cd git-2.14.3/ \
	&& ./configure\
	&& make prefix=/usr/local all \
	&& sudo make prefix=/usr/local install \
	&& bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\"" \
	&& bash -c "debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\"" \
	&& apt-get install -y postfix

RUN mkdir /tmp/ruby
WORKDIR /tmp/ruby
RUN curl --remote-name --progress --progress https://cache.ruby-lang.org/pub/ruby/2.3/ruby-2.3.6.tar.gz \
	&& echo '4e6a0f828819e15d274ae58485585fc8b7caace0  ruby-2.3.6.tar.gz' | shasum -c - \
	&& tar xzf ruby-2.3.6.tar.gz
WORKDIR /tmp/ruby/ruby-2.3.6
RUN ./configure --disable-install-rdoc && make && make install
RUN gem install bundler --no-ri --no-rdoc

RUN mkdir /tmp/go
WORKDIR /tmp/go
RUN curl --remote-name --progress https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz \
	&& echo '1862f4c3d3907e59b04a757cfda0ea7aa9ef39274af99a784f5be843c80c6772  go1.8.3.linux-amd64.tar.gz' | shasum -a256 -c - \
	&& tar -C /usr/local -xzf go1.8.3.linux-amd64.tar.gz \
	&& ln -sf /usr/local/go/bin/{go,godoc,gofmt} /usr/local/bin/ \
	&& rm go1.8.3.linux-amd64.tar.gz

RUN curl --location https://deb.nodesource.com/setup_7.x | sudo bash - \
	&& sudo apt-get install -y nodejs \
	&& curl --silent --show-error https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - \
	&& echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list \
	&& sudo apt-get update -y \
	&& sudo apt-get install -y yarn
	
RUN adduser --disabled-login --gecos 'GitLab' git

WORKDIR /home/git
RUN sudo -u git -H git clone https://gitlab.com/gitlab-org/gitlab-ce.git -b 10-5-stable gitlab

WORKDIR /home/git/gitlab
RUN sudo -u git -H cp  config/gitlab.yml.example config/gitlab.yml
RUN apt-get install -y emacs
RUN sed -i '/    host: localhost/c\    host: 0.0.0.0' config/gitlab.yml \
	&& sed -i '/    # email_enabled: true/c\    email_enabled: false' config/gitlab.yml \
	&& sed -i '/    bin_path: \/usr\/bin\/git/c\    bin_path: \/usr\/local\/bin\/git' config/gitlab.yml

RUN sudo -u git -H cp config/secrets.yml.example config/secrets.yml \
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
	&& sed -i '/  url: redis:\/\/localhost:6379/c\  url: redis:\/\/gitlab:6379' config/resque.yml \
	&& sed -i '/  url: unix:\/var\/run\/redis\/redis.sock/c\  url: redis:\/\/gitlab:6379' config/resque.yml \
	&& sudo -u git -H cp config/database.yml.mysql config/database.yml \
	&& sed -i '/  password: "secure password"/c\  password: "gitlabpwd"' config/database.yml \
	&& sed -i '/  database: gitlabhq_staging/c\  database: gitlab' config/database.yml \
	&& sed -i '/  host: localhost/c\  host: db' config/database.yml \
	&& sed -i '/  database: gitlabhq_test/c\  database: gitlab' config/database.yml \
	&& sed -i '/  password:/c\  password: "gitlabpwd"' config/database.yml

RUN sudo -u git -H bundle install --deployment --without development test postgres aws kerberos
RUN sudo -u git -H bundle exec rake gitlab:shell:install REDIS_URL=redis:redis RAILS_ENV=production SKIP_STORAGE_VALIDATION=true
RUN sudo -u git -H bundle exec rake "gitlab:workhorse:install[/home/git/gitlab-workhorse]" RAILS_ENV=production \
	&& sudo -u git -H bundle exec rake gitlab:setup RAILS_ENV=production
RUN sudo -u git -H bundle exec rake gitlab:setup RAILS_ENV=production GITLAB_ROOT_PASSWORD=mypassword GITLAB_ROOT_EMAIL=vmonteco@student.42.fr \
	&& sudo cp lib/support/init.d/gitlab /etc/init.d/gitlab \
	&& sudo -u git -H bundle exec rake "gitlab:gitaly:install[/home/git/gitaly]" RAILS_ENV=production
RUN sudo chmod 0700 /home/git/gitlab/tmp/sockets/private \
	&& sudo chown git /home/git/gitlab/tmp/sockets/private \
	&& sudo cp lib/support/logrotate/gitlab /etc/logrotate.d/gitlab \
	&& sudo -u git -H bundle exec rake gitlab:env:info RAILS_ENV=production \
	&& sudo -u git -H bundle exec rake gettext:compile RAILS_ENV=production \
	&& sudo -u git -H yarn install --production --pure-lockfile \
	&& sudo -u git -H bundle exec rake gitlab:assets:compile RAILS_ENV=production NODE_ENV=production




	
	

# MAINTAINER <vmonteco@student.42.fr>

# RUN apt-get update -y \
# 	&& apt-get upgrade -y \
# 	&& apt-get install -y sudo curl \
# 	&& bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\" \
#  	&& debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\" \
#  	&& apt-get install -y postfix" \
# 	&& apt-get install -y postfix \
# 	&& curl https://packages.gitlab.com/install/repositories/gitlab/gitlab-ee/script.deb.sh | sudo bash \
# 	&& EXTERNAL_URL="http://localhost" apt-get install -y gitlab-ee
	



# # RUN apt-get update -y \
# # 	&& apt-get upgrade -y \
# # 	&& apt-get install -y sudo build-essential zlib1g-dev libyaml-dev \
# # 	libssl-dev libgdbm-dev libre2-dev libreadline-dev libncurses5-dev \
# # 	libffi-dev curl openssh-server checkinstall libxml2-dev libxslt-dev \
# # 	libcurl4-openssl-dev libicu-dev logrotate rsync python2.7 \
# # 	python-docutils pkg-config cmake git-core

# # RUN bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\" \
# # 	&& debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\" \
# # 	&& apt-get install -y postfix"
# # RUN mkdir /tmp/ruby
# # WORKDIR /tmp/ruby
# # RUN curl --progress ftp://ftp.ruby-lang.org/pub/ruby/2.0/ruby-2.0.0-p353.tar.gz | tar xz
# # WORKDIR /tmp/ruby/ruby-2.0.0-p353
# # RUN ./configure --disable-install-rdoc && make && make install
# # RUN gem install bundler --no-ri --no-rdoc
# # RUN adduser --disabled-login --gecos 'GitLab' git
# # WORKDIR /home/git/
# # RUN sudo -u git -H git clone https://github.com/gitlabhq/gitlab-shell.git -b v1.8.0
# # WORKDIR gitlab-shell
# # RUN sudo -u git -H cp config.yml.example config.yml


# >>>>>>> 95dc7587e3323cac0a1dd257c964568a2962921f
