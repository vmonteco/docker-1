FROM ubuntu
MAINTAINER <vmonteco@student.42.fr>

RUN apt-get update -y \
	&& apt-get upgrade -y \
	&& apt-get install -y sudo curl \
	&& bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\" \
 	&& debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\" \
 	&& apt-get install -y postfix" \
	&& apt-get install -y postfix \
	&& curl https://packages.gitlab.com/install/repositories/gitlab/gitlab-ee/script.deb.sh | sudo bash \
	&& EXTERNAL_URL="http://localhost" apt-get install -y gitlab-ee
	



# RUN apt-get update -y \
# 	&& apt-get upgrade -y \
# 	&& apt-get install -y sudo build-essential zlib1g-dev libyaml-dev \
# 	libssl-dev libgdbm-dev libre2-dev libreadline-dev libncurses5-dev \
# 	libffi-dev curl openssh-server checkinstall libxml2-dev libxslt-dev \
# 	libcurl4-openssl-dev libicu-dev logrotate rsync python2.7 \
# 	python-docutils pkg-config cmake git-core

# RUN bash -c "debconf-set-selections <<< \"postfix postfix/mailname string localhost\" \
# 	&& debconf-set-selections <<< \"postfix postfix/main_mailer_type string 'Internet Site'\" \
# 	&& apt-get install -y postfix"
# RUN mkdir /tmp/ruby
# WORKDIR /tmp/ruby
# RUN curl --progress ftp://ftp.ruby-lang.org/pub/ruby/2.0/ruby-2.0.0-p353.tar.gz | tar xz
# WORKDIR /tmp/ruby/ruby-2.0.0-p353
# RUN ./configure --disable-install-rdoc && make && make install
# RUN gem install bundler --no-ri --no-rdoc
# RUN adduser --disabled-login --gecos 'GitLab' git
# WORKDIR /home/git/
# RUN sudo -u git -H git clone https://github.com/gitlabhq/gitlab-shell.git -b v1.8.0
# WORKDIR gitlab-shell
# RUN sudo -u git -H cp config.yml.example config.yml


