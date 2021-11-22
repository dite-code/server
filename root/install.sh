#!/bin/bash

# Install Packages required for Perfect World server on Debian 8 and Ubuntu 14
# Script courtesy of Wrechid...

set -e

apt-get -y update
apt-get install -y git wget perl unrar nano curl mysql-server apache2 openjdk-7-jre php5 php5-mysql php5-curl phpmyadmin libsvn-java
dpkg --add-architecture i386
apt-get update
apt-get -y install lib32z1 lib32ncurses5
apt-get install -y libgtk2.0-0:i386 libidn11:i386 gstreamer0.10-pulseaudio:i386 gstreamer0.10-plugins-base:i386 gstreamer0.10-plugins-good:i386
apt-get install -y tomcat7



wget https://raw.githubusercontent.com/circulosmeos/gdown.pl/master/gdown.pl
chmod 777 gdown.pl
./gdown.pl https://drive.google.com/file/d/1gH2ZohMEBbFwDLU3huDIghMiB-GWu1Dk/view?usp=sharing pw155.rar
rm gdown.pl

unrar x pw155.rar
rm pw155.rar
tar -zxvf ./pw155_pwAdmin.tar.gz -C /
rm pw155_pwAdmin.tar.gz
rm CPW-Setup.rar
rm extract_PW_tar.sh
rm inst_Debian8_Ubuntu14.sh
rm README.txt

mysql -uroot -pEd2931993@ < /root/db.sql;
rm db.sql

curl -sL https://deb.nodesource.com/setup_14.x -o nodesource_setup.sh
echo "apt-get install --force-yes -y nodejs" >> nodesource_setup.sh
bash nodesource_setup.sh > /dev/null;
rm nodesource_setup.sh;


cd ~
git clone https://github.com/dite-code/server.git > /dev/null;
cd server
cp -r * /
cd ~
rm -r server

cd /home
sed -i -e 's/\r$//' server


#mysqldump -uroot -pEd2931993@ pw > /root/pw.sql

# install the server
#apt update
#apt install mysql-server

# run the wizard
#mysql_secure_installation
#mysql
#mysql> use mysql;
#mysql> SELECT user,authentication_string,plugin,host FROM mysql.user;

# enable password login
#mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'password';
#mysql> FLUSH PRIVILEGES;
#mysql> exit;