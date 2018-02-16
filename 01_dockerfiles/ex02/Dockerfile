FROM ruby
LABEL maintainer "vmonteco@student.42.fr"

RUN gem install rails && gem install bundler
ONBUILD COPY . /opt/app
ONBUILD WORKDIR /opt/app
ONBUILD RUN bundle install
