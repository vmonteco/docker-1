FROM ruby
LABEL maintainer "vmonteco@student.42.fr"

RUN apt-get update -y && apt-get upgrade -y && apt-get install -y nodejs \
	&& gem install bundler
# build then with : docker build -t name --build-arg app=dirname .
ONBUILD ARG app=app
ONBUILD COPY $app /opt/app
ONBUILD WORKDIR /opt/app
ONBUILD RUN bundle install
