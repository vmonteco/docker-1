FROM debian

RUN apt-get update && apt-get install -y openjdk-8-jre
RUN useradd -ms /bin/bash minecraft
WORKDIR /home/minecraft
COPY minecraft_server.1.12.2.jar ./
COPY ops.json ./
RUN chown -Rv minecraft /home/minecraft
USER minecraft
RUN chmod -Rv 700 /home/minecraft && java -Xmx1024M -Xms1024M -jar minecraft_server.1.12.2.jar nogui ; sed -i -e 's/eula=false/eula=true/g' eula.txt && mkdir world

VOLUME /home/minecraft/world
EXPOSE 25565

CMD ["java", "-Xmx1024M", "-Xms1024M", "-jar", "minecraft_server.1.12.2.jar", "nogui"]