![mysqltools](https://banners.beyondco.de/mysqltools.png?theme=light&packageManager=composer+require&packageName=delta-solutions%2Fmysqltools&pattern=architect&style=style_1&description=Mysqltools+is+a+command+line+tool+to+compare+database+structures+and+backup+and+restore+them&md=1&showWatermark=0&fontSize=100px&images=database)
# Mysqltools


![Downloads](https://img.shields.io/packagist/dt/delta-solutions/mysqltools.svg?style=flat-square)

Mysqltools is a command line tool to help you manage your mysql databases.  You can use it 
to compare databases, create database backups, and restore backups.

## Requirements

Mysqltools is a command line tool tested on MacOs desktop and Linux servers.

## Installation

````shell
composer global require delta-solutions/mysqltools
````

## Usage

### Backup a database

command to run: `mysql:backup`
This command dumps the structure of your database to an sql file and stores the data from the database in .csv files.  Files are stored in your Downloads folder.

### Compare two database structures

command to run: `mysql:compare`
This command will compare the structure of two databases and output the differences between your source and target database in a .sql file. The resulting file is stored in your Downloads folder.

### Create an ssh tunnel from your local machine to a mysql server

command to run: `mysql:tunnel`.  This command is handy if you want to connect your application over an ssh tunnel to your database server.  You can for example choose port 13306 to connect to your database ( over ssh ) and use this port in your connection config.  As if you would connect on localhost.
Your (Laravel) application will be able to reach your server even if it's on another location requiring an ssh tunnel.

## Welcome

Once mysqltools is installed you can get an overview of all the commands by running the `mysqltools` command.  You will see this
welcome screen.



