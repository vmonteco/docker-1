FROM debian
LABEL maintainer "vmonteco@student.42.fr"

RUN apt-get update -y && apt-get install -y sqlite3 git nginx bash wget gcc make
RUN adduser --disabled-login --gecos 'Gogs' git
USER git
WORKDIR /home/git
RUN mkdir local
RUN wget https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz
RUN tar -C /home/git/local -xzf go1.8.3.linux-amd64.tar.gz && rm go1.8.3.linux-amd64.tar.gz
ENV GOROOT=/home/git/local/go GOPATH=/home/git/go
ENV PATH=$PATH:$GOROOT/bin:$GOPATH/bin
RUN mkdir -p $GOPATH/src/github.com/gogits
WORKDIR $GOPATH/src/github.com/gogits
RUN git clone --depth=1 -b develop https://github.com/gogits/gogs && cd gogs && go build -tags "sqlite"
USER root
RUN systemctl enable /home/git/go/src/github.com/gogits/gogs/scripts/systemd/gogs.service
USER git
RUN mkdir /home/git/gogs-repositories
WORKDIR $GOPATH/src/github.com/gogits/gogs
EXPOSE 80 3000
CMD ['/home/git/go/src/github.com/gogits/gogs/gogs', 'web']