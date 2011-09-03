#!/bin/bash

# Copyright (C) Sourcemap 2011
# This program is free software: you can redistribute it and/or modify it under the terms
# of the GNU Affero General Public License as published by the Free Software Foundation,
# either version 3 of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License along with this
# program. If not, see <http://www.gnu.org/licenses/>.

while getopts "ho:s:d:u:p" option_name; do
case "$option_name" in
o) oid="$OPTARG";;
s) srchostnm="$OPTARG";;
d) desthostnm="$OPTARG";;
u) username="$OPTARG";;
p) password="$OPTARG";;
[?]) echo "Invalid usage."; exit 1;;
esac
done

./fetch-and-convert-sc "${srchostnm}" "${oid}" | \
    curl -b /tmp/smap-cookies.txt -is -XPOST -d @- -H "Content-Type: application/json" "http://${desthostnm}/services/supplychains/"
