#!/bin/bash

APP="**** INTEGRAÇÃO SG SISTEMAS ****"
echo "$APP"

echo "Insira seu email (github):"
read email

echo "Insira seu username (github):"
read username

echo "Insira seu nome (github):"
read name

echo "Insira seu token (github):"
read token

git clone https://$username:$token@github.com/Inacio-Fernando/integracao-sgsistemas.git
cd integracao-sgsistemas

git config --local user.email $email
git config --local user.name $name

if ! [ -d "logs" ]; then
    mkdir logs
fi

php composer.phar install
cd ..
sudo chmod 777 -R integracao-sgsistemas

