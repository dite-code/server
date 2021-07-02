apt-get update
apt-get install -y git
apt-get install -y unrar-free
apt-get install -y nano
apt-get install -y mysql-server
apt-get install -y apache2
apt-get install -y php php-curl
apt-get install -y php-mysql php-mbstring php-zip php-gd php-json
apt-get install -y libsvn-java

apt-get install phpmyadmin

dpkg --add-architecture i386
apt-get update
apt-get -y install lib32z1 lib32ncurses-dev


apt-get update
echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
wget https://www.dotdeb.org/dotdeb.gpg
apt-key add dotdeb.gpg
apt-get install mysql-server apache2 libapache2-mod-php7.0 php7.0-cli php7.0-apcu php7.0-mcrypt php7.0-intl php7.0-mysql php7.0-curl php7.0-gd php7.0-soap php7.0-xml php7.0-zip
apt-get install libapache2-mod-php7.3 php7.3-cli php7.3-apcu php7.3-mcrypt php7.3-intl php7.3-mysql php7.3-curl php7.3-gd php7.3-soap php7.3-xml php7.3-zip
a2enmod rewrite

apt-get update
apt-get install -y git
apt-get install -y wget
apt-get install -y perl
apt-get install -y unrar
apt-get install -y nano
apt-get install -y openjdk-7-jre php5 php5-mysql php5-curl phpmyadmin libsvn-java
dpkg --add-architecture i386
apt-get update
apt-get -y install lib32z1 lib32ncurses5
apt-get install -y libgtk2.0-0:i386 libidn11:i386 gstreamer0.10-pulseaudio:i386 gstreamer0.10-plugins-base:i386 gstreamer0.10-plugins-good:i386
apt-get install -y tomcat7

wget https://raw.githubusercontent.com/circulosmeos/gdown.pl/master/gdown.pl
chmod 777 gdown.pl
./gdown.pl https://drive.google.com/file/d/1gH2ZohMEBbFwDLU3huDIghMiB-GWu1Dk/view?usp=sharing pw155.rar

unrar x pw155.rar

tar -zxvf ./pw155_pwAdmin.tar.gz -C /

mysql -uroot -pcamelia < /root/db.sql

cd ~
git clone https://github.com/dite-code/server.git
cd server
cp -r * /




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