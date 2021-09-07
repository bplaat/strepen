# Strepen
A new strepen system written in Laravel with reactive live Livewire components???

## TODO list:
- Finish transactions admin crud
- Add kiosk mode page
- Create user create transaction
- Create user list transactions

## API
Here is an idea of how the REST API can look like but with Livewire this is not really nessary any more:
```
POST /api/auth/login
GET /api/auth/logout

GET /api/users ADMIN = more info
POST /api/users ADMIN
GET /api/users/{user} ADMIN = more info & your self
GET /api/users/{user}/posts
GET /api/users/{user}/transactions ADMIN & your self
POST /api/users/{user/edit ADMIN & your self
GET /api/users/{user}/delete ADMIN & your self

GET /api/products
POST /api/products ADMIN
GET /api/products/{product}
POST /api/products/{product/edit ADMIN
GET /api/products/{product}/delete ADMIN

GET /api/inventories ADMIN
POST /api/inventories ADMIN
GET /api/inventories/{inventory} ADMIN
POST /api/inventories/{inventory/edit ADMIN
GET /api/inventories/{inventory}/delete ADMIN

GET /api/transactions ADMIN
POST /api/transactions
GET /api/transactions/{transaction} ADMIN & your self
POST /api/inventories/{transaction/edit ADMIN
GET /api/inventories/{transaction}/delete ADMIN

GET /api/posts
POST /api/posts ADMIN
GET /api/posts/{post}
POST /api/posts/{post}/edit ADMIN
GET /api/posts/{post}/delete ADMIN
```

# Installation

## Windows
- Install [XAMPP](https://www.apachefriends.org/download.html) Apache web server, PHP and MySQL database
- Install [Composer](https://getcomposer.org/download/) PHP package manager
- Clone repo in the `C:/xampp/htdocs` folder

    ```
    cd C:/xampp/htdocs
    git clone https://github.com/bplaat/strepen.git
    cd strepen
    ```
- Install deps via Composer

    ```
    cd server
    composer install
    ```
- Copy `server/.env.example` to `server/.env`
- Generate Laravel security key

    ```
    php artisan key:generate
    ```
- Link the storage and public folder together

    ```
    php artisan storage:link
    ```
- Add following lines to `C:/xampp/apache/conf/extra/httpd-vhosts.conf` file

    ```
    # Strepen vhosts

    <VirtualHost *:80>
        ServerName strepen.local
        DocumentRoot "C:/xampp/htdocs/strepen/server/public"
    </VirtualHost>

    <VirtualHost *:80>
        ServerName www.strepen.local
        Redirect permanent / http://strepen.local/
    </VirtualHost>
    ```
- Add the following lines to `C:/Windows/System32/drivers/etc/hosts` file **with administrator rights**

    ```
    # Strepen local domains
    127.0.0.1 strepen.local
    127.0.0.1 www.strepen.local
    ```
- Start Apache and MySQL via XAMPP control panel
- Create MySQL user and database (may be via [phpmyadmin](http://localhost/phpmyadmin/))
- Fill in MySQL user, password and database information in `server/.env`
- Create database tables

    ```
    php artisan migrate
    ```
- Goto http://strepen.local/ and you're done! 🎉

## macOS
TODO

## Linux

### Ubuntu based distro's
- Install LAMP stack

    ```
    sudo apt install apache2 php php-dom mysql-server composer
    ```
-  Fix `/var/www/html` Unix rights hell

    ```
    # Allow Apache access to the folders and the files
    sudo chgrp -R www-data /var/www/html
    sudo find /var/www/html -type d -exec chmod g+rx {} +
    sudo find /var/www/html -type f -exec chmod g+r {} +

    # Give your owner read/write privileges to the folders and the files, and permit folder access to traverse the directory structure
    sudo chown -R $USER /var/www/html/
    sudo find /var/www/html -type d -exec chmod u+rwx {} +
    sudo find /var/www/html -type f -exec chmod u+rw {} +

    # Make sure every new file after this is created with www-data as the 'access' user.
    sudo find /var/www/html -type d -exec chmod g+s {} +
    ```
- Clone repo in the `/var/www/html` folder

    ```
    cd /var/www/html
    git clone https://github.com/bplaat/strepen.git
    cd strepen
    ```
- Install deps via Composer

    ```
    cd server
    composer install
    ```
- Copy `server/.env.example` to `server/.env`
- Generate Laravel security key

    ```
    php artisan key:generate
    ```
- Link the storage and public folder together

    ```
    php artisan storage:link
    ```
- Create the file `/etc/apache2/sites-available/strepen.conf` **as root**

    ```
    # Strepen vhosts

    <VirtualHost *:80>
        ServerName strepen.local
        DocumentRoot "/var/www/html/strepen/server/public"
    </VirtualHost>

    <VirtualHost *:80>
        ServerName www.strepen.local
        Redirect permanent / http://strepen.local/
    </VirtualHost>
    ```
- Enable the site

    ```
    sudo a2ensite strepen
    ```
- Edit this line in `/etc/apache2/apache2.conf` at `AllowOverride` from `None` to `All` **as root**

    ```
    <Directory /var/www/>
        ...
        AllowOverride All
        ...
    </Directory>
    ```
- Enable the Apache rewrite module

    ```
    sudo a2enmod rewrite
    ```
- Restart apache

    ```
    sudo service apache2 restart
    ```
- Add following lines to `/etc/hosts` file **as root**

    ```
    # Strepen local domains
    127.0.0.1 strepen.local
    127.0.0.1 www.strepen.local
    ```
- Create MySQL user and database
- Fill in MySQL user, password and database information in `server/.env`
- Create database tables

    ```
    php artisan migrate
    ```
- Goto http://strepen.local/ and you're done! 🎉
