# Universal SQL console client 

Purpose is to provide similar-look console client for different SQL servers. It is for you, terminal-geek!

Inspired by mysql client and https://github.com/nitso/colour-mysql-console :)

## Features

- Database support ([as of Doctrine](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/introduction.html)): 
  - MySQL
  - Oracle
  - Microsoft SQL Server
  - PostgreSQL
  - SAP Sybase SQL Anywhere
  - SQLite
  - Drizzle

## Requirements

- PHP 5.3+
- php_pdo extension
- specific database [driver](http://php.net/manual/en/pdo.drivers.php)
- console

## Usage

To launch client just run phar file:
```
$ php console.phar
```

To connect a server you need to specify DSN (http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url):
```
SQL Console > connect mysql://root:password@localhost:3306/TEST
```

To execute a statement use `e` command:

```
SQL Console > e SELECT * FROM test_table
```

## Screens

![basic example](doc/example-screen.png)


## Roadmap
- autocompletion for SQL objects: databases/tables/fields
- autocompletion for SQL statements
- advanced SQL statement execution: prepared statements and bindable parameters
- table/vertical(like mysql \G)/json/php-array data output

## Internals

Following libraries are used:

- https://github.com/symfony/console component for user interaction 
- https://github.com/doctrine/dbal for database connectivity

`\Nitso\SqlConsole\Shell` class was inspired by `Symfony\Component\Console\Shell` and rewritten to modify private (sic!) methods of original class.  

Requires https://github.com/humbug/box for building phar archive. \
Just install project without dev dependencies `composer install --no-dev` and update application with `box compile` 