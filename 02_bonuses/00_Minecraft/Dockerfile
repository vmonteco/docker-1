FROM debian

RUN apt-get update && apt-get install -y openjdk-8-jre
COPY minecraft_server.1.12.2.jar ./

EXPOSE 25565

CMD ["java", "Xmx1024M", "-Xms1024M", "-jar", "minecraft_server.1.12.2.jar", "nogui"]