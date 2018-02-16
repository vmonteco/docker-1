FROM ubuntu

RUN apt-get update -y \
	&& apt-get upgrade -y \
	&& apt-get install -y sudo curl ca-certificates build-essential \
	zlib1g-dev libyaml-dev libssl-dev libgdbm-dev libre2-dev libreadline-dev \
	libncurses5-dev libffi-dev curl openssh-server checkinstall libxml2-dev \
	libxslt-dev libcurl4-openssl-dev libicu-dev logrotate rsync \
	python-docutils pkg-config cmake git-core \
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

