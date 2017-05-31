FROM debian
MAINTAINER <vmonteco:vmonteco@student.42.fr>

RUN useradd -d /opt/teamspeak3-server -m teamspeak3-user
RUN apt-get update -y && apt-get install -y wget bzip2
RUN wget http://dl.4players.de/ts/releases/3.0.13.6/teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2
RUN tar xvjf teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2
RUN cp -r teamspeak3-server_linux_amd64/* /opt/teamspeak3-server
RUN chown teamspeak3-user:teamspeak3-user /opt/teamspeak3-server -R
RUN rm -rf teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2 teamspeak3-server_linux_amd64

CMD ["bash"]