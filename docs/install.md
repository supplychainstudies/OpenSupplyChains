#Open Supply Chains Installation

##Basic Install

0. Download/checkout the code.

1. You'll need to have a basic apache2 setup with php5 setup, if you don't have them already. On Ubuntu, or other Linux distributions which use apt, you can do the following:
	
		apt-get install apache2 apache2-doc apache2-utils libapache2-mod-php5 php5 php5-cli php5-dev

	Additionally you'll want to setup mod-rewrite on apache.
	
		sudo a2enmod rewrite 
		
	After setting DOCROOT to the location of the code change the settings on that directory to `AllowOverrides All` So that rewrite rules in `.htaccess` files will be checked.
	
2. There are a number of dependencies which you'll need. 

		apt-get install php5-gd php5-mcrypt php5-curl php5-pgsql 	
	
##Basic Database Installation

1. Be sure you have PostgreSQL properly installed.  
		
		apt-get install postgresql

2. Install PostGIS.  Most packaged installations will also make available a database template you can use to create a database with GIS features. Make sure you get the right version, like PostGIS 1.4 which is compatible with PostgreSQL 8.4. Install all the necessary files then install its.  
	
		./configure
		make
		make install

3. Create a database user and assign that user a password.  You'll need to be logged in as a root database user or as postgresql in order to have sufficient privileges to complete this step.  Note if the user isn't created as a superuser later steps may fail.  Executing the following commands in most distributions as root will create a user USER and set the user's password:

	sudo -u postgres createuser --superuser USER
 	sudo -u postgres psql 
   	
		postgres=# \password USER
		
 	(Exit from psql using Control-D)
  

4. Create a new database.  On Linux, you can do this as follows:

        createdb -T postgistemplate -O USERfrom3above databasename

5. Create the necessary procedural language on the new database.

		createlang plpgsql databasename

5. Sign into the database you made as the user you created.

        psql --host localhost --user USERfrom3above -d databasename

6. Load the PostGIS functions:

        \i /usr/share/doc/postgresql/8.4/contrib/postgis.sql
        \i /usr/share/doc/postgresql/8.4/contrib/spatial_ref_sys.sql
		\i /usr/share/doc/postgresql/8.4/contrib/postgis_comment.sql
		
	The locations of these files may vary depending on your distribution.  Using Ubuntu 11.04 you would instead enter:
		  
		\i /usr/share/postgresql/8.4/contrib/postgis-1.5/postgis.sql
		\i /usr/share/postgresql/8.4/contrib/postgis-1.5/spatial_ref_sys.sql
		\i /usr/share/postgresql/8.4/contrib/postgis_comments.sql
		  
7. Setup the development configuration for the db-migrate script to use:

		cd /path/to/my/repository/tools/env
		cp dev.example dev
		vi dev  
		
	Change the dev file to reflect your database and username.

8. Execute the db-migrate script in the tools directory:

		cd /path/to/my/repository/tools/
		./db-migrate dev up
   	
9. Alternatively load the core schema and additional sequentially numbered schemas by hand :

        \i /path/to/my/repository/db/schema/00.initial.up.sql  
     	\i /path/to/my/repository/db/schema/00.test_supplychain.up.sql
        ....

   	Be sure you run the commands as the user you hope to use. If you need to change ownership of a table, you can do this with the 'alter table' command:

		alter table tablename owner to 'newowner';  

10. Configure your Kohana/Sourcemap configuration to use this database.
        

##Configure defaults

1.  Create settings files by copying the example files:

		cd /path/to/my/repository/www/application/config
		cp cache.php.sample cache.php
		cp database.php.sample database.php
		cp sourcemap.php.example sourcemap.php
		cp apis.php.sample apis.php
	
2. Edit the database.php file from step 1 to reflect your username, password and database.

3. Create a cache directory writeable by the php code.

		cd /path/to/my/repository/www/application/
		mkdir cache
	
	Give the cache directory appropriate ownership/permissions.  In Ubuntu changing the group to www-data and giving the group rwx permissions suffices.
	
	
##Notes on Database Configuration for Core and Sites

1. If you're editing the core database configuration, do so in the www/application/config/database.php file. You'll need to be sure you use the value PDOPGSQL as the database type. This is a custom class which extends Kohana's PDO library in order to support the lean Kohana ORM.

2. If you're editing a site-specific configuration, this is done the same way as in number one above, but the database.php file goes in `www/sites/mysite/config/`

3. The repository is (sometimes) missing default database.php files in these directories. A sample can be found in `modules/database/`.