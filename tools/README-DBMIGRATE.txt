Database Migration Tool
-----------------------

db-migrate.sh is a barebones schema migration tool.  It expects to
be executed from the tools directory, and it looks for schema and data
files in the db/schema and db/data directories, respectively.  The tool
takes up to three arguments, two of which are required.

Like this:

./db-migrate <environment> <direction> <stop/start index>

Examples:

# migrate upwards, from scratch (run all schema and data files)
./db-migrate dev up

# migrate downwards, to a clean slate (run all schema and data files for "dn" direction)
./db-migrate dev dn

# execute schema "up" files starting with index 02 (inclusive).
./db-migrate dev up 02

# execute schema "dn" files starting from the last one and stopping at index 01 (inclusive).
./db-migrate dev dn 01

This tool saves a database dump including all schema and data in a file marked with the
date and time of execution with the extension ".dump" in the working directory.  It also
saves the output of all psql commands (schema and data files) to dated files with the ".out"
extension.
