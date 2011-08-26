##Directory Structure and Organization

The following top-level directories can be found in the smap repository:

* **db** - database schema, data, fixtures, and other related things
* **tools** - scripts and accessories for deployment and so on
* **docs** - documentation, cheatsheets, etc.
* * **install.md** - Installation instructions
* * **phing.md** - Build instruction for phing 
* * **api.md** - Instructions for the Sourcemap.com API

* **bak** - a place to stash tarballs, etc.
* **t** - tests (in the works, cli)
* **www** - where the Kohana-based application code lives

###Routing/Bootstrapping
If you want custom routing or a non-standard routing, you'll want to put your own site-specific `bootstrap.php` file in your `sites/<siteshortname>` directory.
