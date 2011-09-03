##Directory Structure and Organization

The following top-level directories can be found in the smap repository:

* **www** - where the Kohana-based application code lives
* **docs** - documentation, cheatsheets, etc.
* * **install.md** - Installation instructions
* * **phing.md** - Build instruction for phing 
* * **api.md** - Instructions for the Sourcemap.com API
* **tools** - scripts and accessories for deployment and so on
* **db** - database schema, data, fixtures, and other related things

###Routing/Bootstrapping
If you want custom routing or a non-standard routing, you'll want to put your own site-specific `bootstrap.php` file in your `sites/<siteshortname>` directory.
