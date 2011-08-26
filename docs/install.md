#Sourcemap Installation

##Basic Install

1. You'll need to have a basic apache2 setup with php5 setup, if you don't have them already. On Ubuntu, or other Linux distributions which use apt, you can do the following:
	
		apt-get install apache2 apache2-doc apache2-utils libapache2-mod-php5 php5 php5-cli php5-dev

	Additionally you'll want to setup mod-rewrite on apache.
	
		sudo a2enmod rewrite 
	
2. There are a number of dependencies which you'll need. 

	apt-get install php5-cli php5-dev php5-gd php5-mcrypt php5-curl php5-pgsql 	
	
##Basic Database Installation

1. Be sure you have PostgreSQL properly installed.  I use 8.4. On Ubuntu, or other Linux distributions which use apt, you can do the following:
		
		apt-get install postgresql

2. Install PostGIS.  Most packaged installations will also make available a database template you can use to create a database with GIS features.
	
	Alternatively, you can install PostGIS 1.4 which is compatible with 
	PostgreSQL 8.4. Install all the neccessary files from synaptic then run the below commands.  
	
		./configure
		make
		make install

3. Create a database user.  You'll need to be logged in as a root database user or as postgresql in order to have sufficient privileges to complete this step.

4. Create a new database.  On Linux, you can do this as follows:

        createdb -T postgistemplate -O userfrom3above databasename

5. Create the necessary procedural language on the new database.

		createlang plpgsql databasename

5. Sign into the database you made as the user you created.

        psql --host localhost --user userfrom3above -d databasename

6. Load the PostGIS functions:

        \i /usr/share/doc/postgresql/8.4/contrib/postgis.sql
        \i /usr/share/doc/postgresql/8.4/contrib/spatial_ref_sys.sql
        \i /usr/share/doc/postgresql/8.4/contrib/postgis_comment.sql

7. Load the Sourcemap core schema:

        \i /home/me/path/to/my/repository/db/schema/00_initial.sql   

    *OR* You can use the db-migrate.sh tool in the tools directory.  This script will
    build the latest schema with fixtures and required data.

8. Load additional files, all numbered sequentially.
    *OR* If you used the db-migrate.sh tool, this is already done.

9. Be sure you run the commands as the user you hope to use.  If you need to change  ownership of a table, you can do this with the 'alter table' command:
    
		alter table tablename owner to 'newowner';

10. Configure your Kohana/Sourcemap configuration to use this database.

##Database Configuration for Kohana/Sourcemap Core and Sites  #dbconfig

1. If you're editing the core Sourcemap database configuration, do so in the www/application/config/database.php file. You'll need to be sure you use the value PDOPGSQL as the database type. This is a custom class which extends Kohana's PDO library in order to support the lean Kohana ORM.

2. If you're editing a site-specific configuration, this is done the same way as in number one above, but the database.php file goes in www/sites/mysite/config/.

3. The repository is (sometimes) missing default database.php files in these directories. A sample can be found in modules/database/.