# Async Doctrine Orm

## Disclaimer

This library is a proof of concept, this code is garbage it was just a stupid challenge that someone ask me.

## Requirements 

 * docker
 * php + composer
 * mysql client

## Installation

 * ``docker build -t doctrineasync .``
 * ``composer update --ignore-platform-reqs``
 * ``docker run -p 3306:3306 --name asyncmysql -e MYSQL_ROOT_PASSWORD=root -d mysql:5.7``
 * ``echo "CREATE DATABASE test CHARSET 'UTF8' | mysql -h 127.0.0.1 -u root -proot"``
 * ``docker run -ti -v `pwd`:/app --link asyncmysql:mysql doctrineasync bash``
 
## Usage (when in container)

### Create the schema

```
php doctrine-cli.php orm:schema-tool:create
```

### Testing script

```
php async.php
```
