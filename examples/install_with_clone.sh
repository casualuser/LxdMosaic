#!/usr/bin/env bash
error() {
  printf '\E[31m'; echo "$@"; printf '\E[0m'
}

red=$'\e[1;31m'
grn=$'\e[1;32m'
blu=$'\e[1;34m'
end=$'\e[0m'

if [[ ! $EUID -eq 0 ]]; then
    error "This script should be run using sudo or as the root user"
    exit 1
fi

curl -sL https://deb.nodesource.com/setup_10.x | sudo bash -

# Install Dependecies
apt-get install apache2 php php-cli php-json php-mysql php-dom php-curl unzip zip mysql-server git nodejs -y

npm -g install pm2

# Install composer

##Download composer
curl -sS https://getcomposer.org/installer -o composer-setup.php
## Install
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

## Move the default apache files
a2dissite 000-default
a2dissite default-ssl

# Move to www & clone repository
cd /var/www || exit

git clone https://github.com/turtle0x1/LxdMosaic.git

mkdir -p /var/www/LxdMosaic/src/sensitiveData/certs
mkdir -p /var/www/LxdMosaic/src/sensitiveData/backups
chown -R www-data:www-data /var/www/LxdMosaic/src/sensitiveData/
chown -R www-data:www-data /var/www/LxdMosaic/src/sensitiveData/backups

# Move in LxdManager
cd /var/www/LxdMosaic || exit

git checkout v0.8.0

npm install

# Install Dependecies
composer install

cp .env.dist .env

# Update env values
## DB Host
sed -i -e 's/DB_HOST=.*/DB_HOST=localhost/g' .env
## DB User
sed -i -e 's/DB_USER=.*/DB_USER=lxd/g' .env
## DB Pass
sed -i -e 's/DB_PASS=.*/DB_PASS=lxdManagerPasswordComplex321/g' .env

# Import data into mysql
mysql < sql/users.sql
mysql < sql/seed.sql
mysql < sql/0.1.0.sql
mysql < sql/0.2.0.sql
mysql < sql/0.3.0.sql
mysql < sql/0.5.0.sql
mysql < sql/0.6.0.sql
mysql < sql/0.7.0.sql


cp examples/lxd_manager.conf /etc/apache2/sites-available/

pm2 start node/events.js

pm2 startup

pm2 save

# Add cron job for gathering data
crontab -l | { cat; echo "*/5 * * * * php /var/www/LxdMosaic/src/cronJobs/fleetAnalytics.php"; } | crontab -
crontab -l | { cat; echo "*/1 * * * * php /var/www/LxdMosaic/src/cronJobs/hostsOnline.php"; } | crontab -

# Enable required apache mods
a2enmod ssl
a2enmod headers
a2enmod rewrite
a2enmod proxy
a2enmod proxy_wstunnel
a2enmod proxy_wstunnel
a2enmod proxy_http
a2enmod proxy_balancer

# Enable site
a2ensite lxd_manager

systemctl restart apache2

printf "${grn}\nInstallation successfull \n\n"
printf  "You now need to point your browser at ${blu}https://this_hosts_ip_address${end} ${grn}and accept the self signed certificate${end} \n\n"
printf  "ServerName for LxdManager can be changed in /etc/apache2/sites-available/lxd_manager.conf, followed by an apache restart (systemctl restart apache2) \n\n${end}"
