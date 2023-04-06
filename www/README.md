# Makrab based on Slim Framework 4 Skeleton Application

## Apache2 setup

* In folder /etc/apache2/sites-available copy 000-default.conf to makrab.conf
* Paste this code
```bash
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName makrab.localhost
        DocumentRoot /etc/var/www/html/makrab/public

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
* run this comands
```bash
sudo a2ensite makrab.conf
sudo service apache2 reload
sudo a2enmod rewrite
```

## Mysql

### Init Mysql database
* In mysql terminal:
```
create database makrab;
```
* In Ubuntu terminal:
```
mysql -u root -p makrab < app/StructureMakrab.sql
```
* Enter password

### Connect project to database 
* Connection to mysql in "app" folder.
* Copy db.example.php to db.php and set your credentials.

## Slim Framework setup

Run this command from the directory in which you want to install your new Slim Framework application.

```bash
composer install
```

Phinx migrations
```bash
php vendor/bin/phoenix m
```

## Webix Framework setup
Run this commands from "public" directory

### Install packages
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```
