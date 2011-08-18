#!/bin/bash

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
