![mysqltools](https://banners.beyondco.de/mysqltools.png?theme=light&packageManager=composer+global+require&packageName=delta-solutions%2Fmysqltools&pattern=architect&style=style_1&description=Mysqltools+is+a+command+line+tool+to+compare+database+structures+and+backup+them&md=1&showWatermark=0&fontSize=100px&images=database)
# Mysqltools


![Downloads](https://img.shields.io/packagist/dt/delta-solutions/mysqltools.svg?style=flat-square)

Mysqltools is a versatile command-line tool designed to streamline the management of your MySQL databases. With its user-friendly interface and powerful features, this tool simplifies tasks such as comparing database structures and creating backups.

## Use case

Managing database structures can be challenging, especially when you prefer creating databases in your favorite SQL tool rather than using migrations. Mysqltools solves this problem by allowing you to compare the structure of a source database with a target database. You can easily retrieve SQL statements to synchronize the structures, ensuring consistency between your development and live databases.

## Requirements

Mysqltools is a command-line tool that has been extensively tested on both macOS desktops and Linux servers. Its compatibility with these platforms ensures a seamless experience, providing you with a reliable and efficient solution for your database management needs.

## Installation

````shell
composer global require delta-solutions/mysqltools
````

## Usage

Once mysqltools is installed you can get an overview of all the commands by running the `mysqltools` command.  You will see this
welcome screen.

### The welcome screen

![Mysqltools home screen](brand/brand.png?raw=true "Mysqltools home screen")

### Backup a database

command to run: `mysqltools mysql:backup` or `mt mb`
This command dumps the structure of your database to an SQL file and stores the data from the database in .csv files. The resulting files are stored in your Downloads folder. If you only want to create a backup of the structure without the data, you can add the --nodata option.
If you want to create a backup of only the structure you can add the --nodata option

### Compare two database structures

command to run: `mysqltools mysql:compare` or `mt mc`
This command compares the structure of two databases and outputs the differences between the source and target databases in an SQL file. The resulting file is stored in your Downloads folder.

### Create an ssh tunnel from your local machine to a mysql server

command to run: `mysqltools mysql:tunnel` or `mt mt`.   This command sets up an SSH tunnel from your local machine to a MySQL server. It is useful when you want to connect your application over an SSH tunnel to your database server. By using this command, you can choose a specific port (e.g., 13306) to connect to your database over SSH. You can then configure your application to connect to the local port as if it were connecting to localhost. This allows your application to reach the database server even if it's located on another location that requires an SSH tunnel.




