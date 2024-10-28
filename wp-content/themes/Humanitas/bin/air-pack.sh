#!/bin/bash
# A script that makes a package for WordPress Theme Directory
txtbold=$(tput bold)
boldyellow=${txtbold}$(tput setaf 3)
boldgreen=${txtbold}$(tput setaf 2)
boldwhite=${txtbold}$(tput setaf 7)
yellow=$(tput setaf 3)
green=$(tput setaf 2)
white=$(tput setaf 7)
txtreset=$(tput sgr0)

mkdir -p /var/www
mkdir -p /var/www/airdev
mkdir -p /var/www/airdev/content
mkdir -p /var/www/airdev/content/themes
rm /var/www/airdev/content/themes/humanitas.zip
sh /var/www/airdev/content/themes/humanitas/bin/air-move-out.sh
cd /var/www/airdev/content/themes/
zip -r humanitas.zip humanitas
sh $HOME/air-temp/bin/air-move-in.sh
