#!/bin/bash

RED='\033[1;31m'
NC='\033[0m' # No Color

HOST='cdaem.loc'
HOST2='msk.cdaem.loc'
HOST3='control.cdaem.loc'

if [ "$EUID" -ne 0 ]
    then echo -e "${RED}Please run with sudo or as root${NC}"
  	exit
fi

if grep -q $HOST /etc/hosts
    then echo -e "${RED}Host $HOST already set${NC}"
else
    printf "\n127.0.0.1\t\t$HOST\n" >> /etc/hosts
fi

if grep -q $HOST2 /etc/hosts
    then echo -e "${RED}Host $HOST2 already set${NC}"
else
   printf "\n127.0.0.1\t\t$HOST2\n" >> /etc/hosts
fi

if grep -q $HOST3 /etc/hosts
    then echo -e "${RED}Host $HOST3 already set${NC}"
else
   printf "\n127.0.0.1\t\t$HOST3\n" >> /etc/hosts
fi
