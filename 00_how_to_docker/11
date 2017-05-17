#!/usr/bin/env sh

VBoxManage showvminfo Char --machinereadable | grep SharedFolderName | cut -d "=" -f 2 | sed "s/^\(\"\)\(.*\)\(\"\)\$/\2/g"
