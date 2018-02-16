FROM golang:alpine
MAINTAINER <vmonteco@student.42.fr>

RUN apk update &&  apk add git gcc make bash openssh openssh-client libcap sqlite
RUN mkdir -p $GOPATH/src/github.com/gogits
RUN cd $GOPATH/src/github.com/gogits && git clone --depth=1 -b develop https://github.com/gogits/gogs
WORKDIR  $GOPATH/src/github.com/gogits/gogs
RUN go build
RUN adduser -H -D -u 5993 git -h /srv/git -s /bin/bash && passwd -u git
RUN mkdir /srv/git
RUN mkdir /go/src/github.com/gogits/gogs/log
RUN chown 5993:4207 /srv/git
RUN chown 5993:4207 /go/src/github.com/gogits/gogs/log
RUN chmod -Rv 770 /srv/git
RUN chmod -Rv 770 /go/src/github.com/gogits/gogs/log
#VOLUME /srv/git
RUN mkdir -p $GOPATH/src/github.com/gogits/gogs/custom/conf
RUN mkdir $GOPATH/src/github.com/gogits/gogs/data
RUN chown -Rv 5993:4207 $GOPATH/src/github.com/gogits/gogs
RUN chmod -Rv 700 $GOPATH/src/github.com/gogits/gogs/custom
RUN setcap 'cap_net_bind_service=+ep' $GOPATH/src/github.com/gogits/gogs/gogs
USER git
# app.ini $GOPATH/src/github.com/gogits/gogs/custom/conf/app.ini
EXPOSE 3000
EXPOSE 22
CMD ["./gogs", "web"]
#CMD ["bash"]
