#Open Supply Chains

Open Supply Chains is the opensource codebase behind [Sourcemap][1] that allows anyone to visualize and analyze supply chains. It does this providing a structure for modeling supplychains, tools for richly describing and annotating them with video, images and text, a detailed geographic visualization core, and modules for calculations and display of useful metrics of evaluation (like carbon footprints).

In most cases, this repository will not be directly useful in and of itself. This is a historic release, and the Sourcemap.com api is free, open and accessible. For developers interested in making use of the kind of functionality offered by Sourcemap as it exists, we strongly encourage you to take a look at the [SrcClient][2] repo first. Otherwise, feel free to extend and deploy as needed.

## Quick Install
1. Setup a basic LAPP stack, with a few dependencies.

		apt-get install apache2 apache2-doc apache2-utils libapache2-mod-php5 php5 php5-cli php5-dev php5-gd php5-mcrypt php5-curl php5-pgsql postgresql

2. Install PostGIS (You might find Mapnik's [PostGIS Install Guide][3] helpful here.)
3. Setup the configuration files in `application/config`
4. Install a basic database with `tools/db-migrate`

		./db-migrate dev up

##Structure and Organization

The following interesting directories can be found in the repository:

* **www** - where the Kohana-based application code lives
* * **assets** - Images, scripts and stylesheets.
* * **application** - The core application classes and utilities.
* * **sites** - For extending the core application.
* **docs** - documentation, cheatsheets, etc.
* * **install.md** - Installation instructions
* * **phing.md** - Build instruction for phing 
* * **api.md** - Instructions for the Sourcemap.com API
* **tools** - scripts and accessories for deployment and so on
* **db** - database schema, data, fixtures, and other related things

###Routing/Bootstrapping
If you want custom routing or a non-standard routing, you'll want to put your own site-specific `bootstrap.php` file in your `sites/<siteshortname>` directory.

[1]: http://www.sourcemap.com "Sourcemap.com"
[2]: http://github.com/supplychainstudies/SrcClient "SrcClient on Github"
[3]: http://wiki.openstreetmap.org/wiki/Mapnik/PostGIS "Mapnik PostGIS Guide"