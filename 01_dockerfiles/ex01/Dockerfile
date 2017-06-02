FROM debian
MAINTAINER <vmonteco:vmonteco@student.42.fr>

RUN apt-get update -y && apt-get install -y bzip2
WORKDIR /opt
ADD http://dl.4players.de/ts/releases/3.0.13.6/teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2 .
RUN tar xvjf teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2
RUN useradd -m teamspeak3-user
RUN chown teamspeak3-user -R teamspeak3-server_linux_amd64
RUN rm -rf teamspeak3-server_linux_amd64-3.0.13.6.tar.bz2

EXPOSE 9987 30033 10011 41144 2011-2110

CMD ["su", "teamspeak3-user", "/opt/teamspeak3-server_linux_amd64/ts3server_minimal_runscript.sh", "serveradmin_password=myadminpassword"]