#!/usr/bin/env sh

docker run --rm --name ts3 -d -p 2011-2110:2011-2110/udp -p 9987:9987/udp -p 30033:30033 -p 10011:10011 -p 41144:41144 teamspeak3
